<!DOCTYPE html>
<html>
  <head>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
  </head>
  <body>
    <div id="admin">
      <div id="tools">
        <div id="tool">
          <h1>Step 2: Charge later</h1>
          <fieldset>
            <label>
              <span>Customer</span>
              <input type="text" name="" id="customer-input">
            </label>
            <label>
              <span>Payment Method</span>
              <input type="text" name="" id="payment-method-input">
            </label>
          </fieldset>
          <button id="card-button">Charge Customer</button>
          <div id="card-message"></div>
        </div>
      </div>
    </div>
    <script>
      var customerInput = document.getElementById('customer-input');
      var paymentMethodInput = document.getElementById('payment-method-input');
      var cardButton = document.getElementById('card-button');

      cardButton.addEventListener('click', function(event) {
        event.preventDefault();
        debug('Creating payment intent');
        fetch('/charge_customer', {
          method: 'POST',
          headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
          body: JSON.stringify({
            payment_method: paymentMethodInput.value,
            customer: customerInput.value
          })
        }).then(function(r) {
          return r.json();
        }).then(function(response) {
            console.log(response.status);
          console.log(response);
          if(response.error) {
            debug(response.error.message);
            if(response.error.code === 'authentication_required') {
              debug('Email the customer with this link');
              debug('http://127.0.0.1:8000/complete?pi=' +
                response.error.payment_intent.id + '&pm=' +
                paymentMethodInput.value);
            }
          } else {
            debug('Customer charged!');
          }

        });
      });


      function debug(message) {
        var debugMessage = document.getElementById('card-message');
        console.log('DEBUG: ', message);
        debugMessage.innerText += "\n" + message;
      }
    </script>
  </body>
</html>
