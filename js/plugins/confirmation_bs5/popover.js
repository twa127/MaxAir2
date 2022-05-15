import $ from 'jquery';
import 'bootstrap';

if (typeof $.fn.popover === 'undefined' || $.fn.popover.Constructor.VERSION.split('.').shift() !== '5') {
  throw new Error('Bootstrap Confirmation 5 requires Bootstrap Popover 5');
}

const Popover = $.fn.popover.Constructor;

export default Popover;
