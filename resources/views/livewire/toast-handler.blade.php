<div></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        Livewire.on('show-toast', function(data) {
            console.log(data);
            const message = data[0].message;
            const cssClass = data[0].class;

            const toast = Toastify({
                text: message, // Usa la propiedad directamente
                duration: 2000,
                gravity: 'top',
                position: 'right',
                className: cssClass, // Clase CSS dinámica
                stopOnFocus: true,
                onClick: function() { // Función que se ejecuta al hacer clic
                    toast.hideToast(); // Oculta el toast al hacer clic
                }
            });

            toast.showToast();
        });
    });
</script>
