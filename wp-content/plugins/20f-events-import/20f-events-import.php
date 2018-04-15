<?php
/*
Plugin Name:  20Fathoms Events Calendar
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


//
//
//INIT ADMIN MENU
function twentyf_events_import_register_menu_page() {
  add_menu_page(
    __('20 Fathoms Event Import', 'textdomain'),
    'Events Import',
    'manage_options',
    '20f-events-import.php',
    'twentyf_events_import_admin_page'
  );
}

add_action('admin_menu', 'twentyf_events_import_register_menu_page');


//
//
//ADMIN MENU PAGE VIEW
function twentyf_events_import_admin_page() {
  if(isset($_POST['action']) && ($_POST['action'] != '')) {
    twentyf_events_import_manual();
  }
?>

  <h1>20Fathoms Event Importer</h1>
  <p>Events will import automatically each day.</p>
  <p>In case there is a need to import events manually use the "Import Events" button below to trigger an event import.</p>
  <p><em>Keep in mind that the site will experience performance loss while the import is taking place. Please only import when traffic is low if at all possible.</em></p>
  <form method="post">
    <input type="hidden" name="action" value="import" />
    <?php  wp_nonce_field('start_import', 'events_import'); ?>
    <input type="submit" name="submit" value="Import Events" class="button button-primary" />
  </form>

<?php }


//
//
//MANUAL EVENT IMPORT HOOK
function twentyf_events_import_manual() {
  if(!isset($_POST['action']) || !wp_verify_nonce($_POST['events_import'], 'start_import')) { ?>
    <div class="error">
       <p>Sorry, your nonce was not correct. Please try again.</p>
    </div> <?php exit;
  } else {
    twentyf_events_import();
  }
}

//
//
//REGISTER CRON FOR AUTO IMPORT ON PLUGIN ACTIVATION
register_activation_hook( __FILE__, 'twentyf_events_import_create_schedule' );
function twentyf_events_import_create_schedule() {
  $timestamp = wp_next_scheduled('twentyf_events_import_daily');
  if($timestamp == false) {
    wp_schedule_event(time(), 'daily', 'twentyf_events_import_daily');
  }
}

add_action('twentyf_events_import_daily', 'twentyf_events_import');


//
//
//DEACTIVE CRON WHEN PLUGIN REMOVED
register_deactivation_hook( __FILE__, 'twentyf_events_import_remove_schedule' );
function twentyf_events_import_remove_schedule() {
  wp_clear_scheduled_hook('twentyf_events_import_daily');
}


//
//
//IMPORT FUNCTION
//Separated so it can be uased in the manual hook or with wp_cron
function twentyf_events_import() {
  $meetup_api_key = '54322d712553753b6454422767186237';
  $meetup_topic_category = array(292,122,522);

  $eventbrite_api_key = 'PCRVSIH4LWS2SZVT2BIT';
  $eventbrite_orgid = '14778130821';
  $eventbrite_api_url = 'https://www.eventbriteapi.com/v3/events/search/?organizer.id='.$eventbrite_orgid.'&token='.$eventbrite_api_key;

  //API CALLER
  function curl_api($api_url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $api_url);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
  }

  //EVENT DB POST/PUT
  function insert_events($meta_id, $source, $title, $body, $link, $start_date, $id = 0) {

    $query = new WP_Query(array(
      'post_type' => 'event',
      'meta_query' => array(
        array(
          'key' => 'event_source_id',
          'value' => $meta_id,
        )
      )
    ));

    if($query->have_posts()) {
      while($query->have_posts()): $query->the_post();
        $id = get_the_ID();
      endwhile;
      wp_reset_postdata();
    }
 
    $post_array = array(
      'ID' => $id,
      'post_content' => $body,
      'post_title' => $title,
      'post_status' => 'publish',
      'post_type' => 'event',
      'meta_input' => array(
        'event_link' => $link,
        'event_start_date' => date('Y-m-d H:i:s', strtotime($start_date)),
        'event_source_id' => $meta_id
      )
    );

    if($source == 'eventbrite') {
      $post_array['tax_input'] = array(
        'eventhost' => 3
      );
    }

    wp_insert_post($post_array, true);
  }
  
  //EVENTBRITE DATA FORMATTER
  function insert_eventbrite_events($url) {
    $result = curl_api($url);

    foreach ($result['events'] as $event) {
      $meta_id = $event['id'];
      $source = 'eventbrite';
      $title = $event['name']['text'];
      $body = $event['description']['text'];
      $link = $event['url'];
      $start_date = $event['start']['local'];

      insert_events($meta_id, $source, $title, $body, $link, $start_date);
    }
  }

  //MEETUP DATA FORMATTER
  function insert_meetup_events($url) {
    $result = curl_api($url);
    
    foreach ($result['events'] as $event) {
      $meta_id = $event['id'];
      $source = 'meetup';
      $title = $event['group']['name'];
      $body = $event['description'];
      $link = $event['link'];
      $start_date = $event['local_date'].' '.$event['local_time'];

      insert_events($meta_id, $source, $title, $body, $link, $start_date);
    }
  }


  //INSERT REQUESTS
  insert_eventbrite_events($eventbrite_api_url);

  foreach($meetup_topic_category as $topic_id) {
    $meetup_api_url = 'https://api.meetup.com/find/upcoming_events?&sign=true&lon=-85.618398&topic_category='.$topic_id.'&lat=44.764193&radius=50&key='.$meetup_api_key;

    insert_meetup_events($meetup_api_url);
  }

}
