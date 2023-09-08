<?php
/*
Plugin Name: Fair Labeling
Description: Add custom text below post thumbnails.
Version: 1.0
Author: Your Name
*/

// プラグインの設定メニューを追加
function fair_labeling_plugin_menu() {
    add_options_page(
        'Fair Labeling Settings',
        'Fair Labeling',
        'manage_options',
        'fair-labeling-settings',
        'fair_labeling_settings_page'
    );
}

add_action('admin_menu', 'fair_labeling_plugin_menu');

// 設定メニューページのコンテンツを表示
function fair_labeling_settings_page() {
    ?>
    <div class="wrap">
        <h2>Fair Labeling Settings</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('fair-labeling-settings-group');
            do_settings_sections('fair-labeling-settings');
            
            // 表示オプションを追加
            $show_on_posts = get_option('fair_labeling_show_on_posts');
            $show_on_pages = get_option('fair_labeling_show_on_pages');
            ?>
            
            <h3>Show Custom Text On:</h3>
            
            <label>
                <input type="checkbox" name="fair_labeling_show_on_posts" value="1" <?php checked(1, $show_on_posts); ?> />
                Posts
            </label><br />
            
            <label>
                <input type="checkbox" name="fair_labeling_show_on_pages" value="1" <?php checked(1, $show_on_pages); ?> />
                Pages
            </label><br />
            
            <?php
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// 設定項目を登録
function fair_labeling_register_settings() {
    register_setting('fair-labeling-settings-group', 'fair_labeling_custom_text');
    
    // 新しい設定項目を追加
    register_setting('fair-labeling-settings-group', 'fair_labeling_show_on_posts', 'intval');
    register_setting('fair-labeling-settings-group', 'fair_labeling_show_on_pages', 'intval');
}

add_action('admin_init', 'fair_labeling_register_settings');

// 設定項目を表示
function fair_labeling_settings_field() {
    $custom_text = get_option('fair_labeling_custom_text');
    ?>
    <input type="text" name="fair_labeling_custom_text" value="<?php echo esc_attr($custom_text); ?>" />
    <?php
}

function fair_labeling_settings_section() {
    echo '<p>Enter the custom text to display below post thumbnails:</p>';
}

function fair_labeling_add_settings() {
    add_settings_section('fair-labeling-settings-section', 'Custom Text Settings', 'fair_labeling_settings_section', 'fair-labeling-settings');
    add_settings_field('fair_labeling_custom_text', 'Custom Text', 'fair_labeling_settings_field', 'fair-labeling-settings', 'fair-labeling-settings-section');
}

add_action('admin_init', 'fair_labeling_add_settings');

// アイキャッチの下にカスタムテキストを追加
function fair_labeling_add_custom_text($content) {
    if (has_post_thumbnail()) {
        $show_on_posts = get_option('fair_labeling_show_on_posts');
        $show_on_pages = get_option('fair_labeling_show_on_pages');
        
        // 投稿ページまたは固定ページにカスタムテキストを表示
        if ((is_single() && $show_on_posts) || (is_page() && $show_on_pages)) {
            $custom_text = get_option('fair_labeling_custom_text'); // ユーザーが入力したカスタムテキストを取得
            $custom_text_html = '<div class="fair-labeling-custom-text small-text">' . esc_html($custom_text) . '</div>';
            
            $content = $custom_text_html . $content; // アイキャッチの下にカスタムテキストを追加
        }
    }
    return $content;
}

add_filter('the_content', 'fair_labeling_add_custom_text');

// スタイルシートを読み込む
function fair_labeling_enqueue_styles() {
    wp_enqueue_style('fair-labeling-styles', plugin_dir_url(__FILE__) . 'css/fair-labeling-style.css');
}

add_action('wp_enqueue_scripts', 'fair_labeling_enqueue_styles');
