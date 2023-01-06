/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import "./timesh/vendor/css/core.css"
import "./timesh/vendor/css/theme-default.css"
import "./timesh/css/demo.css"
import "./timesh/vendor/libs/perfect-scrollbar/perfect-scrollbar.css"
import "./timesh/vendor/libs/apex-charts/apex-charts.css"
import "./timesh/vendor/js/helpers.js"
import "./timesh/js/config.js"
import "./timesh/vendor/libs/jquery/jquery.js"
import "./timesh/vendor/libs/popper/popper.js"
import "./timesh/vendor/js/bootstrap.js"
import "./timesh/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"
import "./timesh/vendor/js/menu.js"
import "./timesh/js/main.js"
import "./timesh/js/ui-popover.js"
import "./timesh/vendor/css/pages/page-auth.css"


import 'bootstrap/dist/css/bootstrap.min.css'
import 'fontawesome-free/css/all.min.css'
import '../public/css/jkanban.min.css'
import 'toastr/build/toastr.css'

import toastr from 'toastr'
import { Calendar } from '@fullcalendar/core'
import interactionPlugin from '@fullcalendar/interaction'
import dayGridPlugin from '@fullcalendar/daygrid'

window.toastr = toastr
window.FullCalendar = { Calendar, interactionPlugin, dayGridPlugin }