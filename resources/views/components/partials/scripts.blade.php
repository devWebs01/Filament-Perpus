 <script>
     document.addEventListener('DOMContentLoaded', function() {
         const openMenu = document.getElementById('openMenu');
         const closeMenu = document.getElementById('closeMenu');
         const menu = document.getElementById('menu');
         const section = document.getElementById('section');

         if (openMenu && menu && section) {
             openMenu.addEventListener('click', () => {
                 menu.classList.remove('max-md:w-0');
                 menu.classList.add('max-md:w-full');
                 section.classList.add('overflow-hidden');
             });
         }

         if (closeMenu && menu && section) {
             closeMenu.addEventListener('click', () => {
                 menu.classList.remove('max-md:w-full');
                 menu.classList.add('max-md:w-0');
                 section.classList.remove('overflow-hidden');
             });
         }
     });
 </script>

 <!-- Livewire Alert Script -->
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
