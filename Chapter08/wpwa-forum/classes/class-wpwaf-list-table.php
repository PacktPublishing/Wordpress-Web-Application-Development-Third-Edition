<?php
/*  Copyright 2011  Matthew Van Andel  (email : matt@mattvanandel.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */



/* == NOTICE ===================================================================
 * Please do not alter this file. Instead: make a copy of the entire plugin, 
 * rename it, and work inside the copy. If you modify this plugin directly and 
 * an update is released, your changes will be lost!
 * ========================================================================== */



/* * ************************* LOAD THE BASE CLASS *******************************
 * ******************************************************************************
 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary.
 */
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}




/* * ************************ CREATE A PACKAGE CLASS *****************************
 * ******************************************************************************
 * Create a new list table package that extends the core WP_List_Table class.
 * WP_List_Table contains most of the framework for generating the table, but we
 * need to define and override some methods so that our data can be displayed
 * exactly the way we need it to be.
 * 
 * To display this example on a page, you will first need to instantiate the class,
 * then call $yourInstance->prepare_items() to handle any data manipulation, then
 * finally call $yourInstance->display() to render the table to the page.
 * 
 * Our theme for this list table is going to be movies.
 */

class WPWAF_List_Table extends WP_List_Table {

    /**     * ***********************************************************************
     * Normally we would be querying data from a database and manipulating that
     * for use in your list table. For this example, we're going to simplify it
     * slightly and create a pre-built array. Think of this as the data that might
     * be returned by $wpdb->query().
     *
     * @var array
     * ************************************************************************ */
    var $example_data = array();

    /**     * ***********************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We
     * use the parent reference to set some default configs.
     * ************************************************************************* */
    function __construct() {
        global $status, $page;

        //Set parent defaults
        parent::__construct(array(
                    'singular' => 'topic', //singular name of the listed records
                    'plural' => 'topics', //plural name of the listed records
                    'ajax' => false        //does this table support ajax?
                ));
    }

    /**     * ***********************************************************************
     * Recommended. This method is called when the parent class can't find a method
     * specifically build for a given column. Generally, it's recommended to include
     * one method for each column you want to render, keeping your package class
     * neat and organized. For example, if the class needs to process a column
     * named 'title', it would first see if a method named $this->column_title()
     * exists - if it does, that method will be used. If it doesn't, this one will
     * be used. Generally, you should try to use custom column methods as much as
     * possible.
     *
     * Since we have defined a column_title() method later on, this method doesn't
     * need to concern itself with any column with a name of 'title'. Instead, it
     * needs to handle everything else.
     *
     * For more detailed insight into how columns are handled, take a look at
     * WP_List_Table::single_row_columns()
     *
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     * ************************************************************************ */
    function column_default($item, $column_name) {
        switch ($column_name) {
            case 'topic_title':
                return $item[$column_name];
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }

    /**     * ***********************************************************************
     * Recommended. This is a custom column method and is responsible for what
     * is rendered in any column with a name/slug of 'title'. Every time the class
     * needs to render a column, it first looks for a method named
     * column_{$column_title} - if it exists, that method is run. If it doesn't
     * exist, column_default() is called instead.
     *
     * This example also illustrates how to implement rollover actions. Actions
     * should be an associative array formatted as 'slug'=>'link html' - and you
     * will need to generate the URLs yourself. You could even ensure the links
     *
     *
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     * ************************************************************************ */
    function column_topic_title($item) {
        return sprintf('%1$s ',
                /* $1%s */ $item['topic_title']
        );
    }

    /**     * ***********************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     *
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     * ************************************************************************ */
    function column_cb($item) {
        global $wpdb,$wpwaf;

        $user_id = get_current_user_id();
        $topic_id = $item['ID'];

        $checked = '';
        $topic_subscriptions_table = $wpdb->prefix.'topic_subscriptions'; 
        $sql  = $wpdb->prepare( "SELECT * FROM $topic_subscriptions_table WHERE 
            topic_id = %d AND user_id = %d ", $topic_id , $user_id );
        $result = $wpdb->get_results($sql);
        if($result){
            $checked = ' checked ';
        }

        $topic_visiblity = $wpwaf->restrictions->protection_status($topic_id);
       
        if($topic_visiblity){
            $checkbox_field = '<input type="checkbox" '.$checked.' name="%1$s[]" value="%2$s" />';
        }else{
            $checkbox_field = '';
        }

        return sprintf(
                $checkbox_field,
                /* $1%s */ $this->_args['singular'], 
                /* $2%s */ $item['ID']      
        );
    }

    /**     * ***********************************************************************
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     *
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     *
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     * ************************************************************************ */
    function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'topic_title' => __('Topic Title','wpwaf')
        );
        return $columns;
    }

    /**     * ***********************************************************************
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
     * you will need to register it here. This should return an array where the
     * key is the column that needs to be sortable, and the value is db column to
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     *
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items() and sort
     * your data accordingly (usually by modifying your query).
     *
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     * ************************************************************************ */
    function get_sortable_columns() {
        $sortable_columns = array(
            'topic_title' => array('topic_title', false)
        );
        return $sortable_columns;
    }

    /**     * ***********************************************************************
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     *
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     *
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     *
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     * ************************************************************************ */
    function get_bulk_actions() {
        $actions = array(
            'subscribe' => __('Subscribe','wpwaf'),
            'unsubscribe' => __('Unsubscribe','wpwaf')
        );
        return $actions;
    }

    /**     * ***********************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     *
     * @see $this->prepare_items()
     * ************************************************************************ */
    function process_bulk_action() {
        global $wpdb;
        //Detect when a bulk action is being triggered...
        if ('subscribe' === $this->current_action()) {

            $subscribed_topics = $_POST['topic'];

            $user_id = get_current_user_id();
            $topic_subscriptions_table = $wpdb->prefix.'topic_subscriptions'; 
            foreach ($subscribed_topics as $subscribed_topic) {
                $wpdb->insert(
                        $topic_subscriptions_table,
                        array(
                            'topic_id' => $subscribed_topic,
                            'user_id' => $user_id
                        ),
                        array(
                            '%d',
                            '%d'
                        )
                );
            }

            $msg = __('Succefully completed.','wpwaf') ."<a href='" . admin_url() . "?page=wpwaf_subscriptions'>
                ". __('Subscribe to More Topics','wpwaf') . "</a>";

            wp_die($msg);
        }

        if ('unsubscribe' === $this->current_action()) {

            $subscribed_topics = $_POST['topic'];

            $user_id = get_current_user_id();
            $topic_subscriptions_table = $wpdb->prefix.'topic_subscriptions'; 
            foreach ($subscribed_topics as $subscribed_topic) {
                $wpdb->delete(
                        $topic_subscriptions_table,
                        array( 'topic_id' => $subscribed_topic, 'user_id' => $user_id), array( '%d','%d' )
                );
            }

            $msg = __('Succefully completed.','wpwaf') ."<a href='" . admin_url() . "?page=wpwaf_subscriptions'>
                ". __('Subscribe to More Topics','wpwaf') . "</a>";

            wp_die($msg);
        }
    }

    /**     * ***********************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     *
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     * ************************************************************************ */
    function prepare_items() {
        global $wpdb; //This is used only if making any database queries

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 5;


        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();


        /**
         * REQUIRED. Finally, we build an array to be used by the class for column 
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);


        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();


        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example 
         * package slightly different than one you might build on your own. In 
         * this example, we'll be using array manipulation to sort and paginate 
         * our data. In a real-world implementation, you will probably want to 
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */
        $data = $this->example_data;

        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         *
         * In a real-world situation involving a database, you would probably want
         * to handle sorting by passing the 'orderby' and 'order' values directly
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */
        function usort_reorder($a, $b) {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'user_registered'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order === 'asc') ? $result : -$result; //Send final sort direction to usort
        }

        //usort($data, 'usort_reorder');


        /*         * *********************************************************************
         * ---------------------------------------------------------------------
         * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
         * 
         * In a real-world situation, this is where you would place your query.
         * 
         * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
         * ---------------------------------------------------------------------
         * ******************************************************************** */


        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently 
         * looking at. We'll need this later, so you should always include it in 
         * your own package classes.
         */
        $current_page = $this->get_pagenum();

        /**
         * REQUIRED for pagination. Let's check how many items are in our data array.
         * In real-world use, this would be the total number of items in your database,
         * without filtering. We'll need this later, so you should always include it
         * in your own package classes.
         */
        $total_items = count($data);


        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to 
         */
        $data = array_slice($data, (($current_page - 1) * $per_page), $per_page);



        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where
         * it can be used by the rest of the class.
         */
        $this->items = $data;


        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args(array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page' => $per_page, //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items / $per_page)   //WE have to calculate the total number of pages
        ));
    }

}

function wpwaf_subscribers_menu() {
    add_menu_page( __('Subscribe to Topics','wpwaf'), __('Subscribe to Topics','wpwaf'), 'follow_forum_activities', 'wpwaf_subscriptions', 'topics_list_page');
}

add_action('admin_menu', 'wpwaf_subscribers_menu');


/* * *************************** RENDER TEST PAGE ********************************
 * ******************************************************************************
 * This function renders the admin page and the example list table. Although it's
 * possible to call prepare_items() and display() from the constructor, there
 * are often times where you may need to include logic here between those steps,
 * so we've instead called those methods explicitly. It keeps things flexible, and
 * it's the way the list tables are used in the WordPress core.
 */

function topics_list_page() {

    //Create an instance of our package class...
    $testListTable = new WPWAF_List_Table();

    $topics_query = new WP_Query(array('post_type' => 'wpwaf_topic','post_status' =>'publish' ));

    if($topics_query->have_posts()) : 
      while($topics_query->have_posts()) : $topics_query->the_post();
         array_push($testListTable->example_data, array("ID" => get_the_ID(), "topic_title" => get_the_title()));
      endwhile;
    endif;

    //Fetch, prepare, sort, and filter our data...
    $testListTable->prepare_items();
?>
    <div class="wrap">

        <div id="icon-users" class="icon32"><br/></div>
        <h2><?php echo __('Subscribe to Topics','wpwaf'); ?></h2>


        <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
        <form id="topics-filter" method="POST">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <!-- Now we can render the completed list table -->
            <?php $testListTable->display() ?>
        </form>
    </div>
<?php
}