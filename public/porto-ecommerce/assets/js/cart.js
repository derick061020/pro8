
 function cart_add(data) {

    try {
			
        let array = localStorage.getItem('products_cart');
				console.log(array);
        array = JSON.parse(array);

        let item = JSON.parse(data)

        // El catálogo envía el modelo crudo, cuyo símbolo de moneda viene anidado
        // en currency_type.symbol. El carrito espera un campo plano currency_type_symbol.
        if (!item.currency_type_symbol) {
            item.currency_type_symbol = (item.currency_type && item.currency_type.symbol)
                ? item.currency_type.symbol
                : 'S/';
        }

        let found = array.find(x => x.id == item.id)

        if (!found) {
            array.push(item);
            localStorage.setItem('products_cart', JSON.stringify(array));
            productsCartDropDown();

						jQuery('#moda-succes-add-product').modal('show');
						// setTimeout(function() {
						// 	jQuery('#moda-succes-add-product').modal('hide');
						// }, 3000);
						
            calculateTotalCart();

            $('#product_added').html(`
							<div class="product-single-details-restaurant">
                            <h1 class="product-title">${item.description}</h1>
                            <div class="price-box">
                                <span class="product-price">S/ ${ Number(item.sale_unit_price).toFixed(2) }</span>
                            </div>
                            <div class="product-descrip">
                                <p>${item.name}</p>
                            </div>
								</div>`);

			const addedImagePath = (item.image_medium && item.image_medium !== 'imagen-no-disponible.jpg')
				? `/storage/uploads/items/${item.image_medium}`
				: `/logo/imagen-no-disponible.jpg`;
			$('#product_added_image').html(`<img src="${addedImagePath}" class="img" alt="${item.description}">`)
        }
        else {
            jQuery('#modal-already-product').modal('show');
        }

    } catch ({error}) {
        console.log(error)
    }


}

function productsCartDropDown()
{

	jQuery(".dropdown-cart-products").empty();
	jQuery(".cart-count").empty();
	let count = 0;
	//get data local syrogare prodicts
	let array = localStorage.getItem('products_cart');
	array = JSON.parse(array)
	count = array.length;

	array.forEach(element => {
		const imagePath = (element.image_small && element.image_small !== 'imagen-no-disponible.jpg')
			? `/storage/uploads/items/${element.image_small}`
			: `/logo/imagen-no-disponible.jpg`;

		jQuery(".dropdown-cart-products").append( `
				<div class="product">
					<div class="product-details">
					<h4 class="product-title">
						<a href="$">${element.description}</a>
					</h4>
					<span class="cart-product-info">
						<span class="cart-product-qty">1</span> x ${element.sale_unit_price}
					</span>
					</div>
					<figure class="product-image-container">
						<a href="#" class="product-image">
							<img alt="${element.description}" src="${imagePath}" />
						</a>
						<a href="#" onclick="remove(${element.id})" class="btn-remove" title="Remove Product">
							<i class="icon-cancel"></i>
						</a>
					</figure>
				</div>`
			);
	});

    jQuery(".cart-count").append(count);

}


function calculateTotalCart()
{

	let array = localStorage.getItem('products_cart');
	array = JSON.parse(array);
	let total = 0;
	array.forEach(element => {
		total += parseFloat(element.sale_unit_price)
	});

	$(".cart-total-price").empty();
    $(".cart-total-price").append(total.toFixed(2));


}

// ───────────────────────────────────────────────────────────────────────────
// Vista rápida (quick view): cantidad funcional, precio dinámico y feedback
// "¡Agregado! (N)" sobre el botón. El partial se carga vía AJAX (sin Vue), por
// eso la lógica vive aquí como funciones globales invocadas con onclick/oninput.
// ───────────────────────────────────────────────────────────────────────────
function qvScope(el) {
	return el.closest('[data-qv-scope]');
}

// Normaliza el input a un entero >= 1 y devuelve el valor.
function qvClampQuantity(input) {
	let val = parseInt(input.value, 10);
	if (isNaN(val) || val < 1) val = 1;
	input.value = val;
	return val;
}

// onchange del input: corrige el valor y refresca el precio mostrado.
function qvSync(input) {
	qvClampQuantity(input);
	qvUpdatePrice(qvScope(input));
}

// Botones +/-: ajustan la cantidad sin bajar de 1.
function qvStep(btn, delta) {
	let scope = qvScope(btn);
	let input = scope.querySelector('.qv-quantity');
	let val = parseInt(input.value, 10);
	if (isNaN(val)) val = 1;
	val += delta;
	if (val < 1) val = 1;
	input.value = val;
	qvUpdatePrice(scope);
}

// Recalcula el texto del botón: "Agregar a Carrito · {símbolo} {cantidad×precio}".
// No hace nada mientras se muestra el mensaje "¡Agregado!" (qvBusy).
function qvUpdatePrice(scope) {
	if (!scope || scope.dataset.qvBusy === '1') return;
	let input = scope.querySelector('.qv-quantity');
	let label = scope.querySelector('.qv-add-label');
	let qty = parseInt(input.value, 10) || 1;
	let unit = parseFloat(scope.dataset.unitPrice) || 0;
	let symbol = scope.dataset.symbol || 'S/';
	label.textContent = `Agregar a Carrito · ${symbol} ${(unit * qty).toFixed(2)}`;
}

// Agrega la cantidad elegida al carrito (sumándola a la que ya hubiera),
// y muestra "¡Agregado! (N)" durante 1 segundo antes de volver al precio.
function qvAddToCart(btn) {
	try {
		let scope = qvScope(btn);
		let input = scope.querySelector('.qv-quantity');
		let qty = qvClampQuantity(input);

		let item = JSON.parse(scope.dataset.qvProduct);
		if (!item.currency_type_symbol) {
			item.currency_type_symbol = (item.currency_type && item.currency_type.symbol)
				? item.currency_type.symbol
				: 'S/';
		}

		let array = JSON.parse(localStorage.getItem('products_cart')) || [];
		let found = array.find(x => x.id == item.id);
		if (found) {
			// Suma la cantidad actual a la que ya está en el carrito.
			found.quantity = (parseInt(found.quantity, 10) || 1) + qty;
		} else {
			item.quantity = qty;
			array.push(item);
		}
		localStorage.setItem('products_cart', JSON.stringify(array));

		// Refresca el dropdown/total del header y avisa a las vistas Vue.
		if (typeof productsCartDropDown === 'function') productsCartDropDown();
		if (typeof calculateTotalCart === 'function') calculateTotalCart();
		window.dispatchEvent(new Event('productAddedToCart'));

		// Feedback temporal "¡Agregado! (N)" por 1 segundo.
		let label = scope.querySelector('.qv-add-label');
		scope.dataset.qvBusy = '1';
		label.textContent = `¡Agregado! (${qty})`;
		setTimeout(function () {
			scope.dataset.qvBusy = '0';
			qvUpdatePrice(scope);
		}, 1000);
	} catch (e) {
		console.log(e);
	}
}

function logout()
{
	console.log("register logout")
	$.ajax({
		url: "/ecommerce/logout",
		method: 'get',
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		success: function (data) {
			location.reload()
		},
		error: function (error_data) {

		}
	});
}
