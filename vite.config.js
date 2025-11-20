import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 
                    'resources/js/app.js', 

                    'resources/js/admin/login/validation.js',

                    'resources/js/admin/layout/toggle-sidebar.js',
                    'resources/js/admin/layout/logout-modal.js',
                    'resources/js/admin/layout/notification.js',
 
                    'resources/js/admin/pages/pagination.js',
                    'resources/js/admin/pages/search-bar.js', 
                    'resources/js/admin/pages/alert.js',

                    'resources/js/admin/pages/trips/archived_trips.js', 
                    'resources/js/admin/pages/trips/add-trip-modal.js',
                    'resources/js/admin/pages/trips/edit-trip-modal.js',
                    'resources/js/admin/pages/trips/delete-trip.js',
                    'resources/js/admin/pages/trips/view-trip-modal.js',
                    'resources/js/admin/pages/trips/trips.js',

                    'resources/js/admin/pages/company/add-company-modal.js',
                    'resources/js/admin/pages/company/edit-company-modal.js',
                    'resources/js/admin/pages/company/delete-company.js',
                    'resources/js/admin/pages/company/restore-company.js',
                    
                    'resources/js/admin/pages/no-data.js',
                    'resources/js/admin/pages/dashboard.js',
                    'resources/js/admin/pages/companyFilter.js', 
                    
                    'resources/js/admin/pages/history/view-modal.js',   


                    // Company
                    'resources/js/company/auth/change-password.js',

                    'resources/js/company/login/toggle-password.js',
                    'resources/js/company/login/validation.js',
               
                    'resources/js/company/layout/toggle-sidebar.js',
                    'resources/js/company/layout/logout-modal.js',
                    'resources/js/company/layout/notification.js',

                    'resources/js/company/pages/no-data.js',
                    'resources/js/company/pages/pagination.js',
                    'resources/js/company/pages/dashboard.js', 

                    'resources/js/company/pages/driver/add-driver.js',
                    'resources/js/company/pages/driver/edit-driver.js',
                    'resources/js/company/pages/driver/delete-driver.js',
                    'resources/js/company/pages/driver/restore-driver.js',
                    
                    'resources/js/company/pages/trips/trips.js', 
                    'resources/js/company/pages/trips/view-modal.js',
                    'resources/js/company/pages/trips/assign-driver.js',


                    // Driver 
                    'resources/js/driver/pages/dashboard.js',
                    'resources/js/driver/pages/notification.js',

                    'resources/js/driver/auth/login-validation.js',
                    'resources/js/driver/auth/change-password.js',
 
                    'resources/js/driver/pages/trips/view-modal.js', 
                    'resources/js/driver/pages/trips/confirmation-modal.js', 
                    'resources/js/driver/pages/trips/cancel-modal.js',  
                ],
            refresh: true,
        }),
        tailwindcss(),
    ],

    // server: {
    //     host: '0.0.0.0',
    //     port: 5173, // or your default Vite port
    //     hmr: {
    //         host: '192.168.1.4', // your laptop's local IP address
    //         protocol: 'ws',
    //     },
    // },
});