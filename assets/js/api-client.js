/**
 * Cliente API para el Sistema RRHH Sinforosa
 * Facilita las llamadas AJAX a la API REST
 */

const API = {
    baseUrl: BASE_URL + 'api/',
    
    /**
     * Realizar petición GET
     */
    get: async function(action, params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const url = `${this.baseUrl}?action=${action}&${queryString}`;
        
        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.error || 'Error en la petición');
            }
            
            return data;
        } catch (error) {
            console.error('Error en API GET:', error);
            throw error;
        }
    },
    
    /**
     * Realizar petición POST
     */
    post: async function(action, data = {}) {
        const url = `${this.baseUrl}?action=${action}`;
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            
            const responseData = await response.json();
            
            if (!response.ok) {
                throw new Error(responseData.error || 'Error en la petición');
            }
            
            return responseData;
        } catch (error) {
            console.error('Error en API POST:', error);
            throw error;
        }
    },
    
    // ===== INCIDENCIAS =====
    
    crearIncidencia: function(data) {
        return this.post('crear_incidencia', data);
    },
    
    aprobarIncidencia: function(id) {
        return this.post('aprobar_incidencia', { id });
    },
    
    rechazarIncidencia: function(id) {
        return this.post('rechazar_incidencia', { id });
    },
    
    listarIncidencias: function(empleadoId = null, periodoId = null) {
        return this.get('listar_incidencias', { empleado_id: empleadoId, periodo_id: periodoId });
    },
    
    // ===== PRÉSTAMOS =====
    
    crearPrestamo: function(data) {
        return this.post('crear_prestamo', data);
    },
    
    listarPrestamos: function(empleadoId = null, estatus = 'Activo') {
        return this.get('listar_prestamos', { empleado_id: empleadoId, estatus });
    },
    
    // ===== BONOS =====
    
    crearBono: function(data) {
        return this.post('crear_bono', data);
    },
    
    listarBonos: function(empleadoId = null, limit = 50) {
        return this.get('listar_bonos', { empleado_id: empleadoId, limit });
    },
    
    // ===== ASISTENCIA =====
    
    registrarEntrada: function(empleadoId) {
        return this.post('registrar_asistencia', { empleado_id: empleadoId, tipo: 'entrada' });
    },
    
    registrarSalida: function(empleadoId) {
        return this.post('registrar_asistencia', { empleado_id: empleadoId, tipo: 'salida' });
    },
    
    // ===== VACACIONES =====
    
    aprobarVacaciones: function(id) {
        return this.post('aprobar_vacaciones', { id });
    },
    
    rechazarVacaciones: function(id, comentario = '') {
        return this.post('rechazar_vacaciones', { id, comentario });
    },
    
    // ===== EMPLEADOS =====
    
    buscarEmpleados: function(query) {
        return this.get('buscar_empleados', { q: query });
    }
};

// Funciones de utilidad para mostrar notificaciones
const Notificacion = {
    /**
     * Mostrar notificación de éxito
     */
    exito: function(mensaje, duracion = 3000) {
        this.mostrar(mensaje, 'success', duracion);
    },
    
    /**
     * Mostrar notificación de error
     */
    error: function(mensaje, duracion = 5000) {
        this.mostrar(mensaje, 'error', duracion);
    },
    
    /**
     * Mostrar notificación de advertencia
     */
    advertencia: function(mensaje, duracion = 4000) {
        this.mostrar(mensaje, 'warning', duracion);
    },
    
    /**
     * Mostrar notificación de información
     */
    info: function(mensaje, duracion = 3000) {
        this.mostrar(mensaje, 'info', duracion);
    },
    
    /**
     * Mostrar notificación genérica
     */
    mostrar: function(mensaje, tipo = 'info', duracion = 3000) {
        // Crear contenedor de notificaciones si no existe
        let container = document.getElementById('notificaciones-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'notificaciones-container';
            container.className = 'fixed top-4 right-4 z-50 space-y-2';
            document.body.appendChild(container);
        }
        
        // Colores según tipo
        const colores = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        };
        
        const iconos = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };
        
        // Crear notificación
        const notif = document.createElement('div');
        notif.className = `${colores[tipo]} text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3 max-w-md transform transition-all duration-300 translate-x-full`;
        notif.innerHTML = `
            <i class="fas ${iconos[tipo]} text-xl"></i>
            <span class="flex-1">${mensaje}</span>
            <button onclick="this.parentElement.remove()" class="text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        container.appendChild(notif);
        
        // Animar entrada
        setTimeout(() => {
            notif.classList.remove('translate-x-full');
        }, 10);
        
        // Auto-remover
        if (duracion > 0) {
            setTimeout(() => {
                notif.classList.add('translate-x-full');
                setTimeout(() => notif.remove(), 300);
            }, duracion);
        }
    }
};

// Funciones de utilidad para modales
const Modal = {
    /**
     * Mostrar modal de confirmación
     */
    confirmar: function(mensaje, onConfirm, onCancel = null) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 transform transition-all">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 h-12 w-12 rounded-full bg-yellow-100 flex items-center justify-center">
                        <i class="fas fa-question-circle text-yellow-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Confirmar acción</h3>
                    </div>
                </div>
                <p class="text-gray-600 mb-6">${mensaje}</p>
                <div class="flex justify-end space-x-3">
                    <button id="btn-cancelar" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button id="btn-confirmar" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        Confirmar
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Event listeners
        modal.querySelector('#btn-confirmar').addEventListener('click', () => {
            modal.remove();
            if (onConfirm) onConfirm();
        });
        
        modal.querySelector('#btn-cancelar').addEventListener('click', () => {
            modal.remove();
            if (onCancel) onCancel();
        });
        
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
                if (onCancel) onCancel();
            }
        });
    },
    
    /**
     * Cerrar todos los modales
     */
    cerrarTodos: function() {
        document.querySelectorAll('.modal').forEach(modal => modal.remove());
    }
};

// Exportar para uso global
window.API = API;
window.Notificacion = Notificacion;
window.Modal = Modal;
