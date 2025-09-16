import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import $ from 'jquery'
window.$ = window.jQuery = $;
import 'summernote/dist/summernote-lite';
import 'summernote/dist/summernote-lite.css';