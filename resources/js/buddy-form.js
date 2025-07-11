// buddy-form.js
document.addEventListener('DOMContentLoaded', function() {
    // Datos de zonas por ciudad
    const zonesData = {
        'La Paz': [
            'Centro', 'Rosario', 'San Pedro', 'Gran Poder', 'Tejar', 'Villa Fátima',
            'Miraflores', 'Calacoto', 'San Miguel', 'Sopocachi', 'Obrajes', 'La Florida',
            'Achumani', 'Cota Cota', 'Irpavi', 'San Jorge', 'Seguencoma', 'Mallasa',
            'Zona Sur', 'Alto La Paz', 'Villa El Carmen', 'Max Paredes', 'Periférica'
        ],
        'Cochabamba': [
            'Centro', 'La Recoleta', 'Cala Cala', 'Tupuraya', 'Sarco', 'Alalay',
            'Queru Queru', 'Villa Coronilla', 'Pacata Alta', 'Pacata Baja', 'Villa Punata',
            'Nord', 'Sud', 'Este', 'Oeste', 'Temporal', 'Villa Sebastián Pagador',
            'Villa Granado', 'Villa Moscu', 'Mayorazgo', 'Cañada Strongest'
        ]
    };

    // Base de datos local de calles conocidas para validación offline
    const knownStreets = {
        'Cochabamba': [
            // Calles principales del centro
            'calle 25 de mayo', 'calle españa', 'calle general acha', 'calle bolivar', 'calle sucre',
            'calle ayacucho', 'calle santivañez', 'calle jordan', 'calle venezuela', 'calle colombia',
            'calle brasil', 'calle ecuador', 'calle peru', 'calle mexico', 'calle chile',
            'ismael montes', 'calle ismael montes', 'av ismael montes', 'avenida ismael montes',
            // Avenidas principales
            'avenida america', 'av america', 'avenida heroinas', 'av heroinas', 'avenida ayacucho',
            'avenida oquendo', 'avenida beijing', 'avenida circunvalacion', 'avenida blanco galindo',
            'avenida papa paulo', 'avenida petrolera', 'avenida ramón rivero', 'avenida salvador allende',
            // Calles por zonas
            'calle cochabamba', 'calle tarija', 'calle santa cruz', 'calle potosi', 'calle oruro',
            'calle pando', 'calle beni', 'calle chuquisaca', 'calle la paz',
            // Otras calles conocidas
            'calle hamiraya', 'calle lanza', 'calle esteban arze', 'calle nataniel aguirre',
            'calle calama', 'calle antezana', 'calle luis calvo', 'calle baptista'
        ],
        'La Paz': [
            // Calles del centro
            'calle comercio', 'calle sagarnaga', 'calle linares', 'calle jaen', 'calle sucre',
            'calle potosi', 'calle bolivar', 'calle ayacucho', 'calle yanacocha', 'calle ingavi',
            'calle illimani', 'calle loayza', 'calle mercado', 'calle santa cruz', 'calle cochabamba',
            // Avenidas principales
            'avenida mariscal santa cruz', 'av mariscal santa cruz', 'avenida camacho', 'av camacho',
            'avenida 16 de julio', 'av 16 de julio', 'avenida del ejercito', 'avenida ballivian',
            'avenida arce', 'avenida 6 de agosto', 'avenida costanera', 'avenida hernando siles',
            'avenida los sargentos', 'avenida busch', 'avenida isabel la catolica',
            // Calles zona sur
            'calle 21', 'calle 22', 'calle 26', 'avenida sanchez lima', 'calle rosendo gutierrez',
            'calle fernando guachalla', 'calle pinilla', 'avenida arequipa'
        ]
    };

    let addressValidationTimeout;
    let isAddressValid = false;

    // Elementos del DOM
    const elements = {
        type: document.getElementById('type'),
        disabilityField: document.getElementById('disability-field'),
        disabilityInput: document.getElementById('disability'),
        city: document.getElementById('city'),
        zone: document.getElementById('zone'),
        address: document.getElementById('address'),
        addressLoading: document.getElementById('address-loading'),
        addressValidation: document.getElementById('address-validation'),
        addressValid: document.getElementById('address-valid'),
        addressInvalid: document.getElementById('address-invalid'),
        addressSuggestions: document.getElementById('address-suggestions'),
        buddyForm: document.getElementById('buddyForm')
    };

    // Inicialización
    init();

    function init() {
        // Inicializar el select de zonas como deshabilitado
        elements.zone.disabled = true;
        
        // Agregar event listeners
        setupEventListeners();
    }

    function setupEventListeners() {
        // Mostrar/ocultar campo de discapacidad según el tipo seleccionado
        elements.type.addEventListener('change', handleTypeChange);

        // Actualizar zonas según la ciudad seleccionada
        elements.city.addEventListener('change', handleCityChange);

        // Validación de dirección en tiempo real
        elements.address.addEventListener('input', handleAddressInput);

        // Validación del formulario antes de enviar
        elements.buddyForm.addEventListener('submit', handleFormSubmit);

        // Ocultar sugerencias al hacer clic fuera
        document.addEventListener('click', handleDocumentClick);
    }

    function handleTypeChange() {
        if (elements.type.value === 'buddy') {
            elements.disabilityField.classList.remove('hidden');
            elements.disabilityInput.setAttribute('required', 'required');
        } else {
            elements.disabilityField.classList.add('hidden');
            elements.disabilityInput.removeAttribute('required');
            elements.disabilityInput.value = '';
        }
    }

    function handleCityChange() {
        const selectedCity = elements.city.value;
        
        // Limpiar opciones existentes
        elements.zone.innerHTML = '<option value="">Seleccionar zona</option>';
        
        if (selectedCity && zonesData[selectedCity]) {
            // Agregar zonas de la ciudad seleccionada
            zonesData[selectedCity].forEach(zone => {
                const option = document.createElement('option');
                option.value = zone;
                option.textContent = zone;
                elements.zone.appendChild(option);
            });
            elements.zone.disabled = false;
        } else {
            elements.zone.innerHTML = '<option value="">Primero seleccione una ciudad</option>';
            elements.zone.disabled = true;
        }

        // Limpiar validación de dirección cuando cambia la ciudad
        resetAddressValidation();
    }

    function handleAddressInput() {
        const address = elements.address.value.trim();
        const city = elements.city.value;
        
        if (address.length < 3 || !city) {
            resetAddressValidation();
            return;
        }

        // Debounce para evitar muchas consultas
        clearTimeout(addressValidationTimeout);
        addressValidationTimeout = setTimeout(() => {
            validateAddress(address, city);
        }, 500);
    }

    function handleFormSubmit(e) {
        const address = elements.address.value.trim();
        const city = elements.city.value;
        
        // Solo prevenir envío si definitivamente no es válida y es muy corta
        if (address && city && address.length < 5) {
            e.preventDefault();
            alert('Por favor, ingrese una dirección más específica.');
            return false;
        }
        
        // Permitir envío en la mayoría de casos, incluso si no se pudo verificar
    }

    function handleDocumentClick(event) {
        if (!elements.addressSuggestions.contains(event.target) && event.target !== elements.address) {
            elements.addressSuggestions.classList.add('hidden');
        }
    }

    // Función para validar dirección usando múltiples métodos
    async function validateAddress(address, city) {
        try {
            elements.addressLoading.classList.remove('hidden');
            elements.addressValidation.classList.add('hidden');
            elements.addressSuggestions.classList.add('hidden');
            
            // 1. Validación offline primero (más rápida y confiable para calles conocidas)
            const isKnownStreet = validateOfflineAddress(address, city);
            
            if (isKnownStreet) {
                elements.addressLoading.classList.add('hidden');
                elements.addressValidation.classList.remove('hidden');
                isAddressValid = true;
                elements.addressValid.classList.remove('hidden');
                elements.addressInvalid.classList.add('hidden');
                return;
            }
            
            // 2. Si no se encuentra offline, intentar con API
            await validateOnlineAddress(address, city);
            
        } catch (error) {
            console.error('Error validando dirección:', error);
            // En caso de error, permitir el envío con advertencia
            elements.addressLoading.classList.add('hidden');
            elements.addressValidation.classList.remove('hidden');
            isAddressValid = true; // Ser permisivo en caso de error de API
            elements.addressValid.classList.remove('hidden');
            elements.addressInvalid.classList.add('hidden');
        }
    }

    // Validación offline usando base de datos local
    function validateOfflineAddress(address, city) {
        const normalizedAddress = address.toLowerCase()
            .replace(/\s+/g, ' ')
            .replace(/[.,#]/g, '')
            .trim();
            
        const cityStreets = knownStreets[city] || [];
        
        return cityStreets.some(street => {
            const normalizedStreet = street.toLowerCase();
            // Buscar coincidencias parciales para ser más flexible
            return normalizedAddress.includes(normalizedStreet) || 
                   normalizedStreet.includes(normalizedAddress.split(' ')[0] + ' ' + normalizedAddress.split(' ')[1]);
        });
    }

    // Validación online usando Nominatim
    async function validateOnlineAddress(address, city) {
        // Intentar múltiples queries para mejorar resultados
        const queries = [
            `${address}, ${city}, Bolivia`,
            `${address}, ${city}`,
            `${city}, ${address}, Bolivia`
        ];

        for (let query of queries) {
            try {
                const encodedQuery = encodeURIComponent(query);
                const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodedQuery}&countrycodes=bo&limit=5&addressdetails=1`);
                
                if (!response.ok) continue;
                
                const results = await response.json();
                
                if (results && results.length > 0) {
                    // Verificar si algún resultado coincide con la ciudad o región
                    const validResults = results.filter(result => {
                        const addressData = result.address || {};
                        const resultCity = addressData.city || addressData.town || addressData.village || 
                                         addressData.municipality || addressData.county || addressData.state;
                        
                        return resultCity && (
                            resultCity.toLowerCase().includes(city.toLowerCase()) ||
                            city.toLowerCase().includes(resultCity.toLowerCase())
                        );
                    });
                    
                    if (validResults.length > 0) {
                        elements.addressLoading.classList.add('hidden');
                        elements.addressValidation.classList.remove('hidden');
                        isAddressValid = true;
                        elements.addressValid.classList.remove('hidden');
                        elements.addressInvalid.classList.add('hidden');
                        
                        if (validResults.length > 1) {
                            showAddressSuggestions(validResults);
                        }
                        return;
                    }
                }
            } catch (e) {
                console.log('Query fallida:', query, e);
                continue;
            }
        }
        
        // Si no se encontró nada online, mostrar como no verificado pero permitir envío
        elements.addressLoading.classList.add('hidden');
        elements.addressValidation.classList.remove('hidden');
        isAddressValid = true; // Ser permisivo
        elements.addressValid.classList.add('hidden');
        elements.addressInvalid.classList.remove('hidden');
        
        // Cambiar el mensaje para ser menos restrictivo
        elements.addressInvalid.innerHTML = `
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <span>No se pudo verificar automáticamente, pero puede continuar</span>
        `;
    }

    // Mostrar sugerencias de direcciones
    function showAddressSuggestions(results) {
        elements.addressSuggestions.innerHTML = '';
        
        results.slice(0, 3).forEach(result => {
            const suggestion = document.createElement('div');
            suggestion.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-200 last:border-b-0';
            suggestion.textContent = result.display_name;
            
            suggestion.addEventListener('click', () => {
                elements.address.value = result.display_name.split(',')[0];
                elements.addressSuggestions.classList.add('hidden');
                isAddressValid = true;
                elements.addressValid.classList.remove('hidden');
                elements.addressInvalid.classList.add('hidden');
            });
            
            elements.addressSuggestions.appendChild(suggestion);
        });
        
        if (results.length > 0) {
            elements.addressSuggestions.classList.remove('hidden');
        }
    }

    // Función para resetear validación de dirección
    function resetAddressValidation() {
        elements.addressLoading.classList.add('hidden');
        elements.addressValidation.classList.add('hidden');
        elements.addressSuggestions.classList.add('hidden');
        isAddressValid = false;
    }
});