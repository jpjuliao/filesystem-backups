<?php

/**
 * Plugin Name: Filesystem Backups
 */

new Filesystem_Backups;

class Filesystem_Backups
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'admin_menu']);
        add_action('current_screen', [$this, 'current_screen']);
    }
    public function admin_menu()
    {
        add_menu_page(
            __('Filesystem Backups', 'textdomain'),
            'Filesystem Backups',
            'manage_options',
            'filesystem-backups',
            [$this, 'admin_page'],
            'dashicons-chart-pie',
            6
        );
    }
    public function admin_page()
    {
    }
    public function current_screen($screen)
    {
        if ($screen['id'] == 'filesystem-backups') {
            new Filesystem_Backups_Import;
            new Filesystem_Backups_Export;
        }
    }
}

class Filesystem_Backups_Import
{
    public function __construct()
    {
    }
    public function upload()
    {
    }
    public function get()
    {
        $files = scandir('./');

        foreach ($files as $key => $value) {
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value);

            if (!is_dir($path)) {
                $relative_path = str_replace(getcwd().DIRECTORY_SEPARATOR.'data', '', $path);
                $this->data['raw']['files'][] = $relative_path;
                $this->data['processed']['products'][] = [
                    'post_title' => pathinfo($value, PATHINFO_FILENAME),
                    'post_name' => pathinfo($value, PATHINFO_FILENAME),
                    'image' => $value,
                    'categories' => Filesystem_Backups_Helper::path_to_array($relative_path),
                ];
            } elseif ($value != "." && $value != "..") {
                $this->extract_folder_data($path, $this->data);
                $this->data['raw']['folders'][] = str_replace(getcwd().DIRECTORY_SEPARATOR.'data', '', $path);
            }
        }
    }
    public function prepare()
    {
    }
    public function insert()
    {
        foreach ($this->data['processed']['products'] as $product) {
            $wcproduct = new WC_Product();
            $wcproduct->set_name($product['post_title']);
            $wcproduct->set_slug($product['post_name']);
            $wcproduct->set_image_id($product['image']);
            $wcproduct->set_category_ids($product['categories']);
            $wcproduct->save();
        }
    }
}

class Filesystem_Backups_Export
{
    public function __construct()
    {
    }
    public function get()
    {
    }
    public function prepare()
    {
    }
    public function download()
    {
    }
}

class Filesystem_Backups_Helper
{
    public static function path_to_array($path)
    {
        $terms = explode(DIRECTORY_SEPARATOR, $path);
        for ($i = 0; $i < 2; $i++) {
            array_shift($terms) ;
        }
        array_pop($terms);
        return $terms;
    }
}
