/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import Map from './modules/map'

import $ from 'jquery';
import 'select2/dist/css/select2.min.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';
import 'select2';
import 'slick-carousel'
import 'slick-carousel/slick/slick.css'
import 'slick-carousel/slick/slick-theme.css'

Map.init()

// Address autocomplete (Géoplateforme geocoding API)
function autocompleteAddress(input, onSelect) {
    // Container for the suggestions, displayed under the field
    const list = document.createElement('div')
    list.className = 'list-group position-absolute w-100 shadow'
    list.style.zIndex = 1000
    input.setAttribute('autocomplete', 'off')
    input.parentNode.style.position = 'relative'
    input.parentNode.appendChild(list)

    // Query the API and display up to 5 suggestions
    const search = query => {
        const url =
            'https://geo.api.gouv.fr/communes?nom='
            + encodeURIComponent(query)
            + '&fields=nom,code,codesPostaux,centre'
            + '&boost=population&limit=100&format=json&geometry=centre'

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Erreur HTTP ${response.status}`)
                }

                return response.json()
            })
            .then(data => {
                list.innerHTML = ''

                const normalize = text =>
                    text.normalize('NFD')
                        .replace(/[\u0300-\u036f]/g, '')
                        .toLowerCase()
                        .trim()

                const normalizedQuery = normalize(query)

                const cities = data.filter(city =>
                    normalize(city.nom).startsWith(normalizedQuery)
                )

                cities.slice(0, 5).forEach(city => {
                    const item = document.createElement('button')

                    item.type = 'button'
                    item.className =
                        'list-group-item list-group-item-action'

                    const postcode = city.codesPostaux?.[0] || ''

                    item.textContent = postcode
                        ? `${city.nom} (${postcode})`
                        : city.nom

                    item.addEventListener('click', () => {
                        const coordinates = city.centre?.coordinates

                        if (!coordinates) {
                            console.error(
                                'Coordonnées absentes pour la commune',
                                city.nom
                            )
                            return
                        }

                        const [lng, lat] = coordinates

                        const properties = {
                            name: city.nom,
                            city: city.nom,
                            postcode: postcode
                        }

                        onSelect(properties, lat, lng)
                        list.innerHTML = ''
                    })

                    list.appendChild(item)
                })
            })
            .catch(error => {
                console.error('Erreur API communes :', error)
                list.innerHTML = ''
            })
    }



    // Debounce the input to avoid sending a request on every keystroke
    let timer = null
    input.addEventListener('input', () => {
        clearTimeout(timer)
        const query = input.value.trim()
        if (query.length < 3) {
            list.innerHTML = ''
            return
        }
        timer = setTimeout(() => search(query), 300)
    })

    // Close the suggestions when clicking elsewhere
    document.addEventListener('click', e => {
        if (!input.parentNode.contains(e.target)) {
             list.innerHTML = ''
        }
    })
}


function autocompleteFullAddress(input, onSelect) {
    const list = document.createElement('div')
    list.className = 'list-group position-absolute w-100 shadow'
    list.style.zIndex = 1000

    input.setAttribute('autocomplete', 'off')
    input.parentNode.style.position = 'relative'
    input.parentNode.appendChild(list)

    let timer = null
    let controller = null

    input.addEventListener('input', () => {
        clearTimeout(timer)

        if (controller) {
            controller.abort()
        }

        list.innerHTML = ''

        const query = input.value.trim()
        if (query.length < 3) return

        timer = setTimeout(async () => {
            controller = new AbortController()

            try {
                const url = new URL(
                    'https://data.geopf.fr/geocodage/completion/'
                )
                url.searchParams.set('text', query)
                url.searchParams.set('type', 'StreetAddress')
                url.searchParams.set('maximumResponses', '5')

                const response = await fetch(url, {
                    signal: controller.signal
                })

                if (!response.ok) {
                    throw new Error(`Erreur HTTP ${response.status}`)
                }

                const data = await response.json()

                if (input.value.trim() !== query) return

                list.innerHTML = ''

                ;(data.results || []).forEach(result => {
                    const item = document.createElement('button')
                    item.type = 'button'
                    item.className =
                        'list-group-item list-group-item-action'
                    item.textContent = result.fulltext

                    item.addEventListener('click', async () => {
                        input.value = result.fulltext
                        list.innerHTML = ''

                        try {
                            const searchUrl = new URL(
                                'https://data.geopf.fr/geocodage/search'
                            )
                            searchUrl.searchParams.set('q', result.fulltext)
                            searchUrl.searchParams.set('index', 'address')
                            searchUrl.searchParams.set('limit', '1')

                            const searchResponse = await fetch(searchUrl)

                            if (!searchResponse.ok) {
                                throw new Error(
                                    `Erreur HTTP ${searchResponse.status}`
                                )
                            }

                            const searchData = await searchResponse.json()
                            const feature = searchData.features?.[0]
                            const coordinates =
                                feature?.geometry?.coordinates
                            const properties = feature?.properties || {}

                            if (!coordinates) {
                                console.error(
                                    'Coordonnées introuvables pour cette adresse',
                                    searchData
                                )
                                return
                            }

                            const [lng, lat] = coordinates

                            onSelect({
                                name: result.fulltext,
                                city: properties.city || result.city || '',
                                postcode:
                                    properties.postcode || result.zipcode || ''
                            }, lat, lng)
                        } catch (error) {
                            console.error(
                                'Erreur de géocodage de l’adresse :',
                                error
                            )
                        }
                    })

                    list.appendChild(item)
                })
            } catch (error) {
                if (error.name !== 'AbortError') {
                    console.error(
                        'Erreur API adresses :',
                        error
                    )
                }
            }
        }, 300)
    })

    document.addEventListener('click', event => {
        if (!input.parentNode.contains(event.target)) {
            list.innerHTML = ''
        }
    })
}


 // Property form: full address autocomplete
const inputAddress = document.querySelector('#property_address')

if (inputAddress !== null) {
    autocompleteFullAddress(inputAddress, (p, lat, lng) => {
        inputAddress.value = p.name

        const cityInput = document.querySelector('#property_city')
        const postcodeInput = document.querySelector('#property_postalCode')
        const latInput = document.querySelector('#property_lat')
        const lngInput = document.querySelector('#property_lng')

        if (cityInput) cityInput.value = p.city
        if (postcodeInput) postcodeInput.value = p.postcode
        if (latInput) latInput.value = lat
        if (lngInput) lngInput.value = lng
    })
}

// Search form: only the coordinates are needed
const searchCity = document.querySelector('#city')

if (searchCity !== null) {
    autocompleteAddress(searchCity, (p, lat, lng) => {
        searchCity.value = p.postcode
            ? `${p.name} (${p.postcode})`
            : p.name
        const latInput = document.querySelector('#lat')
        const lngInput = document.querySelector('#lng')

        if (latInput && lngInput) {
            latInput.value = lat
            lngInput.value = lng
        }
    })
    // Typing again invalidates the previously selected coordinates
    searchCity.addEventListener('input', () => {
        const latInput = document.querySelector('#lat')
        const lngInput = document.querySelector('#lng')

        if (latInput) {
            latInput.value = ''
        }

        if (lngInput) {
            lngInput.value = ''
        }
    })
}

$('[data-slider]').slick({
    dots: true,
    arrows: true
})

jQuery(function () {
    $('select').select2({
        placeholder: function () {
            return $(this).data('placeholder') || ''
        },
        allowClear: false
    });
});

let contactButton = $('#contactButton')
contactButton.on('click', e => {
    e.preventDefault();
    $('#contactForm').slideDown();
    contactButton.slideUp();
})

// Delete elements (properties, pictures) through an AJAX request
document.querySelectorAll('[data-delete]').forEach(a => {
    a.addEventListener('click', e => {
        e.preventDefault()
        fetch(a.getAttribute('href'), {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ '_token': a.dataset.token })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    a.parentNode.parentNode.removeChild(a.parentNode)
                } else {
                    alert(data.error)
                }
            })
            .catch(e => alert(e))
    })
})