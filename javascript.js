var multi_products = []
   {% for block in section.blocks %}

     {% assign product_handle = block.settings.block_product %}

     var object = {}

     object.handle = "{{product_handle}}"
     object.url = "{{all_products[product_handle].url }}"
     object.price = {{ all_products[product_handle].price | money_without_currency }}
     object.title = "{{ all_products[product_handle].title }}"
     object.image = "{{ all_products[product_handle].featured_image | img_url: '800x' }}"

     multi_products.push(object)

   {% endfor %} /* We use the all_products object to get the specific product and then the specific values from there. */

   var $product_container = $('#product')

  var initial_product = multi_products[0]

  var initial_product_html = `<div class="product">
    <img src="${initial_product.image}">
    <h3>${initial_product.title}</h3>
    <p>$${initial_product.price}</p>
    <a class="button" href="${initial_product.url}">Shop this product</a>
    </div>
    `

  $product_container.html(initial_product_html) /*we use that initial product object to set the HTML and then serve it into the product container.*/

  $('body').on('click', '.dot', function(){
    $('.dot').removeClass('active')
    $(this).addClass('active')

    var handle = $(this).data('handle')

    var this_product = multi_products.filter((product) => {
      return product.handle === handle
    })[0]


    var product_html = `<div class="product">
      <img src="${this_product.image}">
      <h3>${this_product.title}</h3>
      <p>$${this_product.price}</p>
      <a class="button" href="${initial_product.url}">Shop this product</a>
      </div>
      `

    $product_container.html(product_html)
  }) /*This results in every click of a dot re-writing the product info into the product container.*/

  
