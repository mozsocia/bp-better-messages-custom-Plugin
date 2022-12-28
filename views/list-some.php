<p>This is form submission Practice</p>
<form id="myForm" method="POST">
  <input type="text" name="name" placeholder="Enter your name">
  <input type="email" name="email" placeholder="Enter your email">
  <!-- <input type="hidden" name="nonce" value=/>
  <input type="hidden" name="action" value="my_ajax_form_action"> -->

  <input type="submit" value="Submit">
</form>


<script type="text/javascript">

jQuery(document).ready(function($) {

  //Handle form submission
  $('#myForm').submit(function(e) {
    e.preventDefault();

    let extraData = {
        _moz_nonce:"<?php echo wp_create_nonce('moz_nonce_secret') ?>" ,
        action: 'my_ajax_form_action',
      }

    let data = $(this).serialize()+ '&' + $.param( extraData )
    // console.log(data);

    $.ajax({
      url:'<?php echo admin_url('admin-ajax.php') ?>',
      type: 'POST',
      data: data,
      success: function(response) {
        console.log(response);
        // alert(response);
      },
      error: function(err) {
        console.log(err);
        alert(err.statusText);
      }
    });
  });


});

</script>

