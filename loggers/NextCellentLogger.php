<?php
/**
 * Created by PhpStorm.
 * User: Niko
 * Date: 21/01/2015
 * Time: 21:40
 */

class NextCellentLogger extends SimpleLogger {

	/**
	 * Get array with information about this logger
	 *
	 * @return array
	 */
	function getInfo() {

		$arr_info = array(
			"slug"  => "nextcellent-logger",
			"name" => "NextCellent Logger",
			"description" => __( "Logs events from NextCellent", 'ngg-simple-history'),
			"capability" => "edit_pages",
			"messages" => array(
				'ngg_gallery_created'    => __('Created gallery "{gallery_title}"', 'ngg-simple-history'),
				'ngg_gallery_updated'    => __('Updated gallery "{gallery_title}"', 'ngg-simple-history'),
				'ngg_gallery_deleted'    => __('Deleted gallery #{gallery_id}', 'ngg-simple-history'),
				'ngg_gallery_new_page'   => __('Added a new page to gallery "{gallery_title}"', 'ngg-simple-history'),
				'ngg_images_added'       => __('Added {image_count} image(s) to gallery "{gallery_title}"', 'ngg-simple-history'),
				'ngg_album_added'        => __('Added album "{album_title}" (# {album_id})', 'ngg-simple-history'),
				'ngg_album_updated'      => __('Updated album "{album_title}" (# {album_id})', 'ngg-simple-history'),
				'ngg_album_deleted'      => __('Deleted album #{album_id}', 'ngg-simple-history'),
				'ngg_options_updated'    => __('Updated NextCellent options', 'ngg-simple-history'),
				'ngg_image_deleted'      => __('Deleted image #{image_id}', 'ngg-simple-history'),
				'ngg_image_updated'      => __('Updated image {file_name} (gallery #{gallery_id})', 'ngg-simple-history')
			),
			"labels" => array(
				"search" => array(
					"label" => _x("NextCellent", "NextCellent logger: search", "ngg-simple-history"),
					"options" => array(
						_x("Added gallery", "NextCellent logger: search", "ngg-simple-history") => array(
							"ngg_gallery_created"
						),
						_x("Updated gallery", "NextCellent logger: search", "ngg-simple-history") => array(
							"ngg_gallery_updated"
						),
						_x("Deleted gallery", "NextCellent logger: search", "ngg-simple-history") => array(
							"ngg_gallery_deleted"
						),
						_x("Added new page to gallery", "NextCellent logger: search", "ngg-simple-history") => array(
							"ngg_gallery_new_page"
						),
						_x("Added images", "NextCellent logger: search", "ngg-simple-history") => array(
							"ngg_images_added"
						),
						_x("Added album", "NextCellent logger: search", "ngg-simple-history") => array(
							"ngg_album_added"
						),
						_x("Updated album", "NextCellent logger: search", "ngg-simple-history") => array(
							"ngg_album_updated"
						),
						_x("Deleted album", "NextCellent logger: search", "ngg-simple-history") => array(
							"ngg_album_deleted"
						),
						_x("Updated options", "NextCellent logger: search", "ngg-simple-history") => array(
							"ngg_options_updated"
						),
						_x("Deleted image", "NextCellent logger: search", "ngg-simple-history") => array(
							"ngg_image_deleted"
						),
						_x("Updated image", "NextCellent logger: search", "ngg-simple-history") => array(
							"ngg_image_updated"
						)
					)
				) // end search array
			) // end labels
		);

		return $arr_info;

	}

	public function loaded() {

		add_action("admin_init", array($this, "on_admin_init"));

	}

	function on_admin_init() {

		add_action("ngg_created_new_gallery", array($this, "created_gallery"));
		add_action('ngg_after_new_images_added', array($this, "added_images"), 10, 2);
		add_action('ngg_add_album', array($this, "added_album"));
		add_action( 'ngg_update_album_sortorder', array($this, "updated_album"));
		add_action( 'ngg_delete_album', array($this, "deleted_album"));
		add_action( 'ngg_update_album', array($this, "updated_album"));
		add_action( 'ngg_update_options_page', array($this, "updated_options"));
		add_action( 'ngg_delete_gallery', array( $this, "deleted_gallery" ) );
		add_action( 'ngg_delete_picture', array( $this, "deleted_image") );
		add_action( 'ngg_update_gallery', array( $this, "updated_gallery"));
		add_action( 'ngg_gallery_addnewpage', array( $this, "added_new_page") );
		add_action( 'ngg_image_updated', array( $this, "updated_image" ) );
		add_action( 'ngg_gallery_sort', array( $this, "updated_gallery") );

	}

	public function getLogRowPlainTextOutput($row) {

		$message = $row->message;
		$context = $row->context;


		$message_key = $context["_message_key"];

		// TODO: only link if still available.
		if ( true ) {

			if( $message_key == "ngg_gallery_created") {

				$gallery_id = $context["gallery_id"];
				$context["gallery_link"] = $this->get_gallery_url( $gallery_id );

				$message = __( 'Created gallery "<a href="{gallery_link}">{gallery_title}</a>"', "ngg-simple-history" );

			} elseif ( $message_key == 'ngg_images_added' ) {

				$gallery_id = $context["gallery_id"];
				$context["gallery_link"] = $this->get_gallery_url( $gallery_id );

				$message = __( 'Added {image_count} image(s) to gallery "<a href="{gallery_link}">{gallery_title}</a>"', "ngg-simple-history" );

			} elseif ( $message_key == 'ngg_gallery_updated' ) {

				$gallery_id = $context["gallery_id"];
				$context["gallery_link"] = $this->get_gallery_url( $gallery_id );

				$message = __( 'Updated gallery "<a href="{gallery_link}">{gallery_title}</a>"', "ngg-simple-history" );

			} elseif ( $message_key == 'ngg_gallery_new_page' ) {

				$gallery_id = $context["gallery_id"];
				$context["gallery_link"] = $this->get_gallery_url( $gallery_id );

				$message = __( 'Added new page to gallery "<a href="{gallery_link}">{gallery_title}</a>"', "ngg-simple-history" );

			} elseif ( $message_key == 'ngg_album_added' ) {

				$context["album_link"] = $this->get_album_url();

				$message = __( 'Added album "<a href="{album_link}">{album_title}</a>" (# {album_id})', "ngg-simple-history" );

			} elseif ( $message_key == 'ngg_album_updated') {

				$context["album_link"] = $this->get_album_url();

				$message = __( 'Updated album "<a href="{album_link}">{album_title}</a>" (# {album_id})', "ngg-simple-history" );

			} elseif ($message_key == 'ngg_album_deleted' ) {

				$message = __( 'Deleted album # {album_id}', "ngg-simple-history" );

			} elseif ( $message_key == 'ngg_options_updated' ) {

				$message = __('Updated NextCellent options', 'ngg-simple-history');

			} elseif ( $message_key == 'ngg_gallery_deleted' ) {

				$message = __('Deleted gallery #{gallery_id}', 'ngg-simple-history');

			} elseif ( $message_key == 'ngg_image_deleted' ) {

				$message = __('Deleted image #{image_id}', 'ngg-simple-history');

			} elseif ( $message_key == 'ngg_image_updated' ) {

				$gallery_id = $context["gallery_id"];
				$context["gallery_link"] = $this->get_gallery_url( $gallery_id );

				$message = __( 'Updated image <a href="{url}">{file_name}</a> (gallery <a href="{gallery_link}">{gallery_title}</a>)', "ngg-simple-history" );

			}

			$message = $this->interpolate( $message, $context );

		} else {

			// Attachment post is not available, attachment has probably been deleted
			$message = parent::getLogRowPlainTextOutput( $row );

		}

		return $message;

	}

	/**
	 * Get an url to the gallery.
	 *
	 * @param int $gallery_id The gallery id.
	 *
	 * @return string The URL.
	 */
	private function get_gallery_url( $gallery_id ) {
		if( is_network_admin() ) {
			$url = network_admin_url('admin.php?page=nggallery-manage-gallery&mode=edit&gid=' . $gallery_id);
		} else {
			$url = admin_url('admin.php?page=nggallery-manage-gallery&mode=edit&gid=' . $gallery_id);
		}

		return $url;
	}

	/**
	 * Get an url to the album page.
	 *
	 * @return string The URl.
	 */
	private function get_album_url() {

		if( is_network_admin() ) {
			$url = network_admin_url('admin.php?page=nggallery-manage-album');
		} else {
			$url = admin_url('admin.php?page=nggallery-manage-album');
		}

		return $url;
	}

	/**
	 * Called when a gallery is added
	 */
	function created_gallery($gallery_id) {

		$gallery = nggdb::find_gallery($gallery_id);

		$this->infoMessage(
			'ngg_gallery_created',
			array(
				"gallery_id" => $gallery_id,
				"gallery_title" => $gallery->title,
				"gallery_description" => $gallery->gal_desc
			)
		);

	}

	function added_images( $gallery_id, $image_ids ) {

		$gallery = nggdb::find_gallery($gallery_id);

		$image_count = count($image_ids);

		$this->infoMessage(
			'ngg_images_added',
			array(
				"gallery_id"    => $gallery_id,
				"gallery_title" => $gallery->title,
				"image_count"   => $image_count,
				"image_ids"     => $image_ids
			)
		);


	}

	function added_album( $album_id ) {
		$album = nggdb::find_album($album_id);

		$this->infoMessage(
			'ngg_album_added',
			array(
				"album_id"              => $album_id,
				"album_title"           => $album->name,
				"album_desccription"    => $album->albumdesc
			)
		);
	}

	function updated_album( $album_id ) {
		$album = nggdb::find_album($album_id);

		$this->infoMessage(
			'ngg_album_updated',
			array(
				"album_id"              => $album_id,
				"album_title"           => $album->name,
				"album_desccription"    => $album->albumdesc
			)
		);
	}

	function deleted_album( $album_id ) {

		$this->infoMessage(
			'ngg_album_deleted',
			array(
				"album_id" => $album_id
			)
		);
	}

	function updated_options() {
		$this->infoMessage( 'ngg_options_updated' );
	}

	function deleted_gallery( $gallery_id ) {

		$this->infoMessage(
			'ngg_gallery_deleted',
			array(
				"gallery_id" => $gallery_id
			)
		);
	}

	function deleted_image( $image_id ) {

		$this->infoMessage(
			'ngg_image_deleted',
			array(
				"image_id" => $image_id
			)
		);
	}

	function updated_gallery($gallery_id) {

		$gallery = nggdb::find_gallery($gallery_id);

		$this->infoMessage(
			'ngg_gallery_updated',
			array(
				"gallery_id" => $gallery_id,
				"gallery_title" => $gallery->title,
				"gallery_description" => $gallery->gal_desc
			)
		);

	}

	function added_new_page($gallery_id) {

		$gallery = nggdb::find_gallery($gallery_id);

		$this->infoMessage(
			'ngg_gallery_new_page',
			array(
				"gallery_id" => $gallery_id,
				"gallery_title" => $gallery->title
			)
		);

	}

	function updated_image($image) {

		$this->infoMessage(
			'ngg_image_updated',
			array(
				"gallery_id"    => $image->galleryid,
				"gallery_title" => $image->title,
				"picture_id"    => $image->pid,
				"file_name"     => $image->filename,
				"url"           => $image->imageURL,
				"description"   => $image->description,
				"image_date"    => $image->imagedate
			)
		);

	}

}