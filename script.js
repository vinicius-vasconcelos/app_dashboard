$(document).ready(() => {
	$('#documentacao').on('click', () => {
		$('#pagina').load('documentacao.html') //usa GET(obj AJAX)
	})

	$('#suporte').on('click', () => {
		$.get('suporte.html', responseText => $('#pagina').html(responseText)) //obj AJAX pela sintaxe GET
	})

	//AJAX
	$('select#competencia').on('change', e => {
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: 'app.php',
			data: `competencia=${$(e.target).val()}`, //formato do navegador = x-www-form-urlencoded(recupera todos os dados de form da página)
			success: respText => {
				console.log(respText);
				$('#numeroVendas').html(respText.numeroVendas)
				$('#totalVendas').html(respText.totalVendas)
				
			},
			error: erro => console.log(erro)
		})
	})
})