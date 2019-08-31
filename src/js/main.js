'use strict';

function admin() {
	
	const dataBg = document.querySelectorAll('[data-bg-wpapw]');
	dataBg.forEach( function(el) {
		var attr = el.getAttribute('data-bg-wpapw')
		el.style.backgroundImage = 'url('+ attr +')';
	});
	    
}

function ready(fn) {
	if (document.readyState != 'loading') {
		fn();
    } else {
		document.addEventListener('DOMContentLoaded', fn);
	}
}

function completeAjax(fn) {
	const send = XMLHttpRequest.prototype.send
    XMLHttpRequest.prototype.send = function() { 
        this.addEventListener('load', function() {
            fn();
        })
        return send.apply(this, arguments)
    }
}

window.ready( function() {
    admin();
});

window.completeAjax( function() {
    admin();
});
