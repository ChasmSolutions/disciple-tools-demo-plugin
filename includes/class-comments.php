<?php

/**
 * DT_Demo_Comments
 *
 * @class DT_Demo_Comments
 * @version    0.1
 * @since 0.1
 * @package    Disciple_Tools
 * @author Chasm.Solutions & Kingdom.Training
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly
}

class DT_Demo_Comments {

    /**
     * DT_Demo_Comments The single instance of DT_Demo_Comments.
     * @var     object
     * @access  private
     * @since     0.1
     */
    private static $_instance = null;

    /**
     * Main DT_Demo_Comments Instance
     *
     * Ensures only one instance of DT_Demo_Comments is loaded or can be loaded.
     *
     * @since 0.1
     * @static
     * @return DT_Demo_Comments instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    /**
     * Constructor function.
     * @access  public
     * @since   0.1
     */
    public function __construct() {
    }

    /**
     * Loops location creation according to supplied $count.
     * @param $count    int Number of records to create.
     * @return string
     */
    public function add_comments( $loops = 100 ){
        // Get list of records
        $args = array(
            'numberposts'   => -1,
            'post_type'   => 'contacts'
        );
        $contacts = get_posts( $args );

        $args = array(
            'fields'       => 'all',
            'count_total'  => true,
        );
        $users = get_users( $args );


        if ($loops > 100) {
            $loops = 100;
        }

        if (count( $contacts ) < $loops) {
            $loops = count( $contacts );
        }

        $user_count = count( $users );

        shuffle( $contacts );
        shuffle( $users );

        $i = 0;
        $time = current_time( 'mysql' );

        while ($loops > $i) {

            $user_data = $users[rand( 0, $user_count - 1 )]->data;

            $data = array(
                'comment_post_ID' => $contacts[$i]->ID,
                'comment_author' => $user_data->display_name,
                'comment_author_email' => $user_data->user_email,
                'comment_content' => dt_demo_comment_ipsum(),
                'comment_type' => '',
                'comment_parent' => 0,
                'user_id' => $user_data->ID,
                'comment_date' => $time,
                'comment_approved' => 1,
            );

            $comment_id = wp_insert_comment( $data );
            add_comment_meta( $comment_id, '_sample', 'sample' );

            $i++;
        }

        return $i . ' comments added randomly.';
    }

    /**
     * Delete all comments in database
     * @return string
     */
    public function delete_comments() {

        $args = array(
            'meta_key' => '_sample',
        );
        $comments = get_comments( $args );

        foreach ($comments as $comment) {
            $id = $comment->comment_ID;

            wp_delete_comment( $id, true );
        }

        return 'Comments deleted';

    }

}