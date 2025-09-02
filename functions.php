<?php
/**
 * Кастомный тип записи для музыки с аудиоплеером
 */

// Регистрация кастомного типа записи "Музыка"
function register_music_post_type() {
    $labels = array(
        'name'                  => 'Музыка',
        'singular_name'         => 'Музыкальная композиция',
        'menu_name'             => 'Музыка',
        'name_admin_bar'        => 'Музыкальная композиция',
        'archives'              => 'Архив музыки',
        'attributes'            => 'Атрибуты музыки',
        'parent_item_colon'     => 'Родительская композиция:',
        'all_items'             => 'Вся музыка',
        'add_new_item'          => 'Добавить новую композицию',
        'add_new'               => 'Добавить новую',
        'new_item'              => 'Новая композиция',
        'edit_item'             => 'Редактировать композицию',
        'update_item'           => 'Обновить композицию',
        'view_item'             => 'Просмотреть композицию',
        'view_items'            => 'Просмотреть музыку',
        'search_items'          => 'Поиск музыки',
        'not_found'             => 'Не найдено',
        'not_found_in_trash'    => 'Не найдено в корзине',
        'featured_image'        => 'Обложка композиции',
        'set_featured_image'    => 'Установить обложку',
        'remove_featured_image' => 'Удалить обложку',
        'use_featured_image'    => 'Использовать как обложку',
        'insert_into_item'      => 'Вставить в композицию',
        'uploaded_to_this_item' => 'Загружено в эту композицию',
        'items_list'            => 'Список композиций',
        'items_list_navigation' => 'Навигация по списку композиций',
        'filter_items_list'     => 'Фильтровать список композиций',
    );

    $args = array(
        'label'                 => 'Музыка',
        'description'           => 'Музыкальные композиции с аудиоплеером',
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail'),
        'taxonomies'            => array('music_genre'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-format-audio',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
    );

    register_post_type('music', $args);
}
add_action('init', 'register_music_post_type', 0);

// Регистрация таксономии для жанров музыки
function register_music_genre_taxonomy() {
    $labels = array(
        'name'                       => 'Жанры музыки',
        'singular_name'              => 'Жанр музыки',
        'menu_name'                  => 'Жанры',
        'all_items'                  => 'Все жанры',
        'parent_item'                => 'Родительский жанр',
        'parent_item_colon'          => 'Родительский жанр:',
        'new_item_name'              => 'Новое название жанра',
        'add_new_item'               => 'Добавить новый жанр',
        'edit_item'                  => 'Редактировать жанр',
        'update_item'                => 'Обновить жанр',
        'view_item'                  => 'Просмотреть жанр',
        'separate_items_with_commas' => 'Разделить жанры запятыми',
        'add_or_remove_items'        => 'Добавить или удалить жанры',
        'choose_from_most_used'      => 'Выбрать из часто используемых',
        'popular_items'              => 'Популярные жанры',
        'search_items'               => 'Поиск жанров',
        'not_found'                  => 'Не найдено',
        'no_terms'                   => 'Нет жанров',
        'items_list'                 => 'Список жанров',
        'items_list_navigation'      => 'Навигация по списку жанров',
    );

    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'show_in_rest'               => true,
    );

    register_taxonomy('music_genre', array('music'), $args);
}
add_action('init', 'register_music_genre_taxonomy', 0);

// Добавление мета-полей для музыки
function add_music_meta_boxes() {
    add_meta_box(
        'music_audio_file',
        'MP3 файл',
        'music_audio_file_callback',
        'music',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_music_meta_boxes');

// Callback для мета-поля MP3 файла
function music_audio_file_callback($post) {
    wp_nonce_field('music_audio_file_nonce', 'music_audio_file_nonce');
    $audio_file = get_post_meta($post->ID, '_music_audio_file', true);
    ?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="music_audio_file">MP3 файл</label>
            </th>
            <td>
                <input type="url" id="music_audio_file" name="music_audio_file" value="<?php echo esc_attr($audio_file); ?>" class="regular-text" />
                <input type="button" class="button" id="upload_audio_button" value="Выбрать файл" />
                <p class="description">Выберите MP3 файл для этой композиции.</p>
                <?php if ($audio_file): ?>
                    <audio controls style="margin-top: 10px;">
                        <source src="<?php echo esc_url($audio_file); ?>" type="audio/mpeg">
                        Ваш браузер не поддерживает аудио элемент.
                    </audio>
                <?php endif; ?>
            </td>
        </tr>
    </table>
    
    <script>
    jQuery(document).ready(function($) {
        $('#upload_audio_button').click(function(e) {
            e.preventDefault();
            
            var audio_uploader = wp.media({
                title: 'Выберите MP3 файл',
                button: {
                    text: 'Использовать этот файл'
                },
                multiple: false,
                library: {
                    type: 'audio'
                }
            });
            
            audio_uploader.on('select', function() {
                var attachment = audio_uploader.state().get('selection').first().toJSON();
                $('#music_audio_file').val(attachment.url);
            });
            
            audio_uploader.open();
        });
    });
    </script>
    <?php
}

// Сохранение мета-полей
function save_music_meta($post_id) {
    if (!isset($_POST['music_audio_file_nonce']) || !wp_verify_nonce($_POST['music_audio_file_nonce'], 'music_audio_file_nonce')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (isset($_POST['post_type']) && 'music' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return;
        }
    } else {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    if (isset($_POST['music_audio_file'])) {
        update_post_meta($post_id, '_music_audio_file', sanitize_url($_POST['music_audio_file']));
    }
}
add_action('save_post', 'save_music_meta');

// Шорткод для вывода музыки
function music_player_shortcode($atts) {
    $atts = shortcode_atts(array(
        'posts_per_page' => 9,
        'genre' => '',
        'orderby' => 'date',
        'order' => 'DESC'
    ), $atts);

    $args = array(
        'post_type' => 'music',
        'posts_per_page' => intval($atts['posts_per_page']),
        'orderby' => $atts['orderby'],
        'order' => $atts['order'],
        'post_status' => 'publish'
    );

    if (!empty($atts['genre'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'music_genre',
                'field' => 'slug',
                'terms' => $atts['genre']
            )
        );
    }

    $music_posts = get_posts($args);
    
    if (empty($music_posts)) {
        return '<p>Музыка не найдена.</p>';
    }

    ob_start();
    ?>
    <div class="music-player-container">
        <div class="music-grid">
            <?php foreach ($music_posts as $post): 
                setup_postdata($post);
                $audio_file = get_post_meta($post->ID, '_music_audio_file', true);
                $thumbnail = get_the_post_thumbnail($post->ID, 'medium');
            ?>
                <div class="music-item">
                    <div class="music-cover">
                        <?php if ($thumbnail): ?>
                            <?php echo $thumbnail; ?>
                        <?php else: ?>
                            <div class="music-placeholder">
                                <span class="dashicons dashicons-format-audio"></span>
                            </div>
                        <?php endif; ?>
                        <div class="music-overlay">
                            <button class="play-button" data-audio="<?php echo esc_url($audio_file); ?>">
                                <span class="play-icon">▶</span>
                            </button>
                        </div>
                    </div>
                    <div class="music-info">
                        <h3 class="music-title"><?php echo get_the_title($post->ID); ?></h3>
                        <div class="music-genres">
                            <?php
                            $genres = get_the_terms($post->ID, 'music_genre');
                            if ($genres && !is_wp_error($genres)) {
                                $genre_names = array();
                                foreach ($genres as $genre) {
                                    $genre_names[] = $genre->name;
                                }
                                echo implode(', ', $genre_names);
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Аудиоплеер -->
        <div class="audio-player" id="audioPlayer">
            <div class="player-controls">
                <button class="control-btn prev-btn" id="prevBtn">⏮</button>
                <button class="control-btn play-pause-btn" id="playPauseBtn">▶</button>
                <button class="control-btn next-btn" id="nextBtn">⏭</button>
            </div>
            <div class="player-info">
                <div class="current-track" id="currentTrack">Выберите трек</div>
                <div class="track-progress">
                    <div class="progress-bar">
                        <div class="progress" id="progress"></div>
                    </div>
                    <div class="time-display">
                        <span id="currentTime">0:00</span>
                        <span id="duration">0:00</span>
                    </div>
                </div>
            </div>
            <div class="player-volume">
                <span class="volume-icon">🔊</span>
                <input type="range" class="volume-slider" id="volumeSlider" min="0" max="100" value="70">
            </div>
        </div>
        
        <audio id="audio" preload="metadata"></audio>
    </div>

    <style>
    .music-player-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .music-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .music-item {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
    }

    .music-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .music-cover {
        position: relative;
        width: 100%;
        height: 200px;
        overflow: hidden;
    }

    .music-cover img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .music-item:hover .music-cover img {
        transform: scale(1.05);
    }

    .music-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 48px;
    }

    .music-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .music-item:hover .music-overlay {
        opacity: 1;
    }

    .play-button {
        background: rgba(255, 255, 255, 0.9);
        border: none;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 20px;
        color: #333;
    }

    .play-button:hover {
        background: white;
        transform: scale(1.1);
    }

    .music-info {
        padding: 15px;
    }

    .music-title {
        margin: 0 0 8px 0;
        font-size: 16px;
        font-weight: 600;
        color: #333;
        line-height: 1.4;
    }

    .music-genres {
        font-size: 14px;
        color: #666;
    }

    /* Аудиоплеер */
    .audio-player {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 20px;
        color: white;
        display: flex;
        align-items: center;
        gap: 20px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    }

    .player-controls {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .control-btn {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        border-radius: 50%;
        width: 45px;
        height: 45px;
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .control-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.05);
    }

    .play-pause-btn {
        width: 55px;
        height: 55px;
        font-size: 20px;
        background: rgba(255, 255, 255, 0.3);
    }

    .player-info {
        flex: 1;
    }

    .current-track {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .track-progress {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .progress-bar {
        flex: 1;
        height: 6px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 3px;
        overflow: hidden;
        cursor: pointer;
    }

    .progress {
        height: 100%;
        background: white;
        border-radius: 3px;
        width: 0%;
        transition: width 0.1s ease;
    }

    .time-display {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.8);
        white-space: nowrap;
    }

    .player-volume {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .volume-icon {
        font-size: 16px;
    }

    .volume-slider {
        width: 80px;
        height: 4px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 2px;
        outline: none;
        cursor: pointer;
    }

    .volume-slider::-webkit-slider-thumb {
        appearance: none;
        width: 16px;
        height: 16px;
        background: white;
        border-radius: 50%;
        cursor: pointer;
    }

    .volume-slider::-moz-range-thumb {
        width: 16px;
        height: 16px;
        background: white;
        border-radius: 50%;
        cursor: pointer;
        border: none;
    }

    /* Адаптивность */
    @media (max-width: 768px) {
        .music-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .audio-player {
            flex-direction: column;
            gap: 15px;
            padding: 15px;
        }

        .player-controls {
            order: 1;
        }

        .player-info {
            order: 2;
            width: 100%;
        }

        .player-volume {
            order: 3;
            width: 100%;
            justify-content: center;
        }

        .volume-slider {
            width: 120px;
        }

        .music-cover {
            height: 150px;
        }

        .music-title {
            font-size: 14px;
        }
    }

    @media (max-width: 480px) {
        .music-grid {
            grid-template-columns: 1fr;
        }

        .music-player-container {
            padding: 10px;
        }

        .control-btn {
            width: 40px;
            height: 40px;
            font-size: 14px;
        }

        .play-pause-btn {
            width: 50px;
            height: 50px;
            font-size: 18px;
        }
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const audio = document.getElementById('audio');
        const playPauseBtn = document.getElementById('playPauseBtn');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const progress = document.getElementById('progress');
        const currentTime = document.getElementById('currentTime');
        const duration = document.getElementById('duration');
        const volumeSlider = document.getElementById('volumeSlider');
        const currentTrack = document.getElementById('currentTrack');
        const progressBar = document.querySelector('.progress-bar');

        let currentTrackIndex = 0;
        let isPlaying = false;
        let tracks = [];

        // Собираем все треки
        document.querySelectorAll('.play-button').forEach((btn, index) => {
            const audioSrc = btn.getAttribute('data-audio');
            const trackTitle = btn.closest('.music-item').querySelector('.music-title').textContent;
            tracks.push({
                src: audioSrc,
                title: trackTitle,
                element: btn
            });
        });

        // Форматирование времени
        function formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        }

        // Загрузка трека
        function loadTrack(index) {
            if (tracks[index]) {
                audio.src = tracks[index].src;
                currentTrack.textContent = tracks[index].title;
                currentTrackIndex = index;
                
                // Обновляем активный элемент
                document.querySelectorAll('.music-item').forEach(item => {
                    item.classList.remove('active');
                });
                tracks[index].element.closest('.music-item').classList.add('active');
            }
        }

        // Воспроизведение/пауза
        function togglePlayPause() {
            if (isPlaying) {
                audio.pause();
                playPauseBtn.textContent = '▶';
                isPlaying = false;
            } else {
                audio.play();
                playPauseBtn.textContent = '⏸';
                isPlaying = true;
            }
        }

        // Следующий трек
        function nextTrack() {
            currentTrackIndex = (currentTrackIndex + 1) % tracks.length;
            loadTrack(currentTrackIndex);
            if (isPlaying) {
                audio.play();
            }
        }

        // Предыдущий трек
        function prevTrack() {
            currentTrackIndex = (currentTrackIndex - 1 + tracks.length) % tracks.length;
            loadTrack(currentTrackIndex);
            if (isPlaying) {
                audio.play();
            }
        }

        // Обновление прогресса
        function updateProgress() {
            const progressPercent = (audio.currentTime / audio.duration) * 100;
            progress.style.width = progressPercent + '%';
            currentTime.textContent = formatTime(audio.currentTime);
        }

        // Установка прогресса
        function setProgress(e) {
            const width = progressBar.clientWidth;
            const clickX = e.offsetX;
            const duration = audio.duration;
            audio.currentTime = (clickX / width) * duration;
        }

        // События
        playPauseBtn.addEventListener('click', togglePlayPause);
        nextBtn.addEventListener('click', nextTrack);
        prevBtn.addEventListener('click', prevTrack);
        progressBar.addEventListener('click', setProgress);
        volumeSlider.addEventListener('input', function() {
            audio.volume = this.value / 100;
        });

        audio.addEventListener('timeupdate', updateProgress);
        audio.addEventListener('loadedmetadata', function() {
            duration.textContent = formatTime(audio.duration);
        });
        audio.addEventListener('ended', nextTrack);

        // Клик по треку
        document.querySelectorAll('.play-button').forEach((btn, index) => {
            btn.addEventListener('click', function() {
                loadTrack(index);
                audio.play();
                playPauseBtn.textContent = '⏸';
                isPlaying = true;
            });
        });

        // Установка начального уровня громкости
        audio.volume = 0.7;
    });
    </script>
    <?php

    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('music_player', 'music_player_shortcode');

// Добавление стилей в админку
function music_admin_styles() {
    global $post_type;
    if ($post_type == 'music') {
        echo '<style>
            .music-admin-meta {
                background: #f9f9f9;
                padding: 15px;
                border-radius: 5px;
                margin: 10px 0;
            }
            .music-admin-meta label {
                font-weight: bold;
                display: block;
                margin-bottom: 5px;
            }
            .music-admin-meta input[type="url"] {
                width: 100%;
                max-width: 500px;
            }
        </style>';
    }
}
add_action('admin_head', 'music_admin_styles');
?>