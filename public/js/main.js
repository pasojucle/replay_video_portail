$( function() { 
    $('.js-datepicker').datepicker({
        format: 'yyyy-mm-dd'
    });
	$(document).on('submit', 'form[name="version"]', addVersion)

} );


jQuery(function($){
	$.datepicker.regional['fr'] = {
		closeText: 'Fermer',
		prevText: '&#x3c;Préc',
		nextText: 'Suiv&#x3e;',
		currentText: 'Aujourd\'hui',
		monthNames: ['Janvier','Fevrier','Mars','Avril','Mai','Juin',
		'Juillet','Aout','Septembre','Octobre','Novembre','Decembre'],
		monthNamesShort: ['Jan','Fev','Mar','Avr','Mai','Jun',
		'Jul','Aou','Sep','Oct','Nov','Dec'],
		dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
		dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
		dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
		weekHeader: 'Sm',
		dateFormat: 'dd/mm/yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: '',
		minDate: '-12M +0D',
		maxDate: '+12M +0D',
		numberOfMonths: 1,
		showButtonPanel: false
	};
	$.datepicker.setDefaults($.datepicker.regional['fr']);
});

function addVersion(e) {
	e.preventDefault();
	const form = $(this);
	$.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: form.serialize(),
		dataType: "json",
        success: function (response) {
			if (response.redirect === null) {
				$('.modal').find('form').replaceWith($(response.html).find('form'));
			} else {
				window.location.replace(response.redirect);
			}
        }
    });
}