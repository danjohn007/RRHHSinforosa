/**
 * Validaciones del Sistema RRHH Sinforosa
 * Contiene todas las funciones de validación y utilidades JavaScript
 */

const Validaciones = {
    /**
     * Validar teléfono a 10 dígitos
     */
    validarTelefono: function(telefono) {
        const regex = /^[0-9]{10}$/;
        return regex.test(telefono);
    },
    
    /**
     * Validar email
     */
    validarEmail: function(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    },
    
    /**
     * Validar CURP (18 caracteres)
     */
    validarCURP: function(curp) {
        const regex = /^[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9A-Z][0-9]$/;
        return regex.test(curp);
    },
    
    /**
     * Validar RFC (13 caracteres para persona física, 12 para moral)
     */
    validarRFC: function(rfc) {
        const regex12 = /^[A-ZÑ&]{3}[0-9]{6}[A-Z0-9]{3}$/; // Persona moral
        const regex13 = /^[A-ZÑ&]{4}[0-9]{6}[A-Z0-9]{3}$/; // Persona física
        return regex12.test(rfc) || regex13.test(rfc);
    },
    
    /**
     * Validar NSS (11 dígitos)
     */
    validarNSS: function(nss) {
        const regex = /^[0-9]{11}$/;
        return regex.test(nss);
    },
    
    /**
     * Validar código postal (5 dígitos)
     */
    validarCodigoPostal: function(cp) {
        const regex = /^[0-9]{5}$/;
        return regex.test(cp);
    },
    
    /**
     * Validar que un número sea positivo
     */
    validarNumeroPositivo: function(numero) {
        return !isNaN(numero) && parseFloat(numero) > 0;
    },
    
    /**
     * Validar fecha (no puede ser futura para fecha de nacimiento)
     */
    validarFechaNacimiento: function(fecha) {
        const fechaNac = new Date(fecha);
        const hoy = new Date();
        return fechaNac < hoy;
    },
    
    /**
     * Validar rango de edad (mínimo 18 años)
     */
    validarEdadMinima: function(fechaNacimiento, edadMinima = 18) {
        const hoy = new Date();
        const fechaNac = new Date(fechaNacimiento);
        const edad = hoy.getFullYear() - fechaNac.getFullYear();
        const mes = hoy.getMonth() - fechaNac.getMonth();
        
        if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNac.getDate())) {
            return edad - 1 >= edadMinima;
        }
        return edad >= edadMinima;
    },
    
    /**
     * Formatear teléfono mientras se escribe (XXX-XXX-XXXX)
     */
    formatearTelefono: function(input) {
        let valor = input.value.replace(/\D/g, '');
        if (valor.length > 10) valor = valor.substring(0, 10);
        
        if (valor.length >= 6) {
            valor = valor.substring(0, 3) + '-' + valor.substring(3, 6) + '-' + valor.substring(6);
        } else if (valor.length >= 3) {
            valor = valor.substring(0, 3) + '-' + valor.substring(3);
        }
        
        input.value = valor;
    },
    
    /**
     * Limpiar formato de teléfono para enviar
     */
    limpiarTelefono: function(telefono) {
        return telefono.replace(/\D/g, '');
    },
    
    /**
     * Validar CLABE interbancaria (18 dígitos)
     */
    validarCLABE: function(clabe) {
        const regex = /^[0-9]{18}$/;
        return regex.test(clabe);
    },
    
    /**
     * Mostrar mensaje de error en un campo
     */
    mostrarError: function(campo, mensaje) {
        // Remover error anterior si existe
        this.limpiarError(campo);
        
        // Agregar clase de error
        campo.classList.add('border-red-500');
        
        // Crear mensaje de error
        const errorDiv = document.createElement('div');
        errorDiv.className = 'text-red-500 text-sm mt-1 error-message';
        errorDiv.textContent = mensaje;
        
        // Insertar después del campo
        campo.parentNode.appendChild(errorDiv);
    },
    
    /**
     * Limpiar mensaje de error de un campo
     */
    limpiarError: function(campo) {
        campo.classList.remove('border-red-500');
        const errorMsg = campo.parentNode.querySelector('.error-message');
        if (errorMsg) {
            errorMsg.remove();
        }
    },
    
    /**
     * Limpiar todos los errores de un formulario
     */
    limpiarErroresFormulario: function(formulario) {
        const campos = formulario.querySelectorAll('.border-red-500');
        campos.forEach(campo => this.limpiarError(campo));
    },
    
    /**
     * Validar formulario de empleado
     */
    validarFormularioEmpleado: function(formulario) {
        let valido = true;
        this.limpiarErroresFormulario(formulario);
        
        // Validar campos requeridos
        const nombres = formulario.querySelector('[name="nombres"]');
        if (!nombres.value.trim()) {
            this.mostrarError(nombres, 'El nombre es requerido');
            valido = false;
        }
        
        const apellidoPaterno = formulario.querySelector('[name="apellido_paterno"]');
        if (!apellidoPaterno.value.trim()) {
            this.mostrarError(apellidoPaterno, 'El apellido paterno es requerido');
            valido = false;
        }
        
        // Validar teléfono si está presente
        const telefono = formulario.querySelector('[name="telefono"]');
        if (telefono && telefono.value && !this.validarTelefono(this.limpiarTelefono(telefono.value))) {
            this.mostrarError(telefono, 'El teléfono debe tener 10 dígitos');
            valido = false;
        }
        
        const celular = formulario.querySelector('[name="celular"]');
        if (celular && celular.value && !this.validarTelefono(this.limpiarTelefono(celular.value))) {
            this.mostrarError(celular, 'El celular debe tener 10 dígitos');
            valido = false;
        }
        
        // Validar email
        const email = formulario.querySelector('[name="email_personal"]');
        if (email && email.value && !this.validarEmail(email.value)) {
            this.mostrarError(email, 'El email no es válido');
            valido = false;
        }
        
        // Validar CURP
        const curp = formulario.querySelector('[name="curp"]');
        if (curp && curp.value && !this.validarCURP(curp.value.toUpperCase())) {
            this.mostrarError(curp, 'El CURP no es válido (18 caracteres)');
            valido = false;
        }
        
        // Validar RFC
        const rfc = formulario.querySelector('[name="rfc"]');
        if (rfc && rfc.value && !this.validarRFC(rfc.value.toUpperCase())) {
            this.mostrarError(rfc, 'El RFC no es válido');
            valido = false;
        }
        
        // Validar NSS
        const nss = formulario.querySelector('[name="nss"]');
        if (nss && nss.value && !this.validarNSS(nss.value)) {
            this.mostrarError(nss, 'El NSS debe tener 11 dígitos');
            valido = false;
        }
        
        // Validar fecha de nacimiento
        const fechaNacimiento = formulario.querySelector('[name="fecha_nacimiento"]');
        if (fechaNacimiento && fechaNacimiento.value) {
            if (!this.validarEdadMinima(fechaNacimiento.value, 18)) {
                this.mostrarError(fechaNacimiento, 'El empleado debe ser mayor de 18 años');
                valido = false;
            }
        }
        
        // Validar código postal
        const codigoPostal = formulario.querySelector('[name="codigo_postal"]');
        if (codigoPostal && codigoPostal.value && !this.validarCodigoPostal(codigoPostal.value)) {
            this.mostrarError(codigoPostal, 'El código postal debe tener 5 dígitos');
            valido = false;
        }
        
        // Validar salarios
        const salarioMensual = formulario.querySelector('[name="salario_mensual"]');
        if (salarioMensual && salarioMensual.value && !this.validarNumeroPositivo(salarioMensual.value)) {
            this.mostrarError(salarioMensual, 'El salario debe ser mayor a 0');
            valido = false;
        }
        
        return valido;
    },
    
    /**
     * Solo permitir números en un campo
     */
    soloNumeros: function(event) {
        const charCode = event.which ? event.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            event.preventDefault();
            return false;
        }
        return true;
    },
    
    /**
     * Solo permitir letras y espacios
     */
    soloLetras: function(event) {
        const charCode = event.which ? event.which : event.keyCode;
        const char = String.fromCharCode(charCode);
        if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]$/.test(char)) {
            event.preventDefault();
            return false;
        }
        return true;
    },
    
    /**
     * Convertir a mayúsculas automáticamente
     */
    convertirMayusculas: function(input) {
        input.value = input.value.toUpperCase();
    },
    
    /**
     * Confirmar acción
     */
    confirmarAccion: function(mensaje) {
        return confirm(mensaje);
    }
};

// Aplicar validaciones automáticas al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    
    // Formatear teléfonos automáticamente
    document.querySelectorAll('input[name="telefono"], input[name="celular"]').forEach(input => {
        input.addEventListener('input', function() {
            Validaciones.formatearTelefono(this);
        });
        
        input.addEventListener('keypress', Validaciones.soloNumeros);
    });
    
    // Solo números en campos específicos
    document.querySelectorAll('input[name="nss"], input[name="codigo_postal"]').forEach(input => {
        input.addEventListener('keypress', Validaciones.soloNumeros);
    });
    
    // Convertir a mayúsculas CURP y RFC
    document.querySelectorAll('input[name="curp"], input[name="rfc"]').forEach(input => {
        input.addEventListener('input', function() {
            Validaciones.convertirMayusculas(this);
        });
    });
    
    // Solo letras en nombres
    document.querySelectorAll('input[name="nombres"], input[name="apellido_paterno"], input[name="apellido_materno"]').forEach(input => {
        input.addEventListener('keypress', Validaciones.soloLetras);
    });
    
    // Validar formularios de empleados
    document.querySelectorAll('form[id*="empleado"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!Validaciones.validarFormularioEmpleado(this)) {
                e.preventDefault();
                alert('Por favor corrija los errores en el formulario');
                return false;
            }
        });
    });
    
    // Limpiar formatos antes de enviar formularios
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            // Limpiar formato de teléfonos
            this.querySelectorAll('input[name="telefono"], input[name="celular"]').forEach(input => {
                if (input.value) {
                    input.value = Validaciones.limpiarTelefono(input.value);
                }
            });
        });
    });
});

// Exportar objeto para uso global
window.Validaciones = Validaciones;
