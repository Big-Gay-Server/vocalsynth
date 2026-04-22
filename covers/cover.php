<h2>【UTAU カバー】 <?= $cover_data['title'] ?> 【<?= $cover_data['voicebank'] ?>】</h2>
<div class="video-container">
    <iframe src="<?= $cover_data['embed_url'] ?>" frameborder="0" allowfullscreen loading="lazy"></iframe>
</div>
<div class='description'>
    <h3>"<?= $cover_data['raw']['lyric for desc'] ?? '' ?>"</h3>
    <p><?= $cover_data['raw']['comments'] ?? '' ?></p>
</div>
<div class='credits'>
    <h3> ⦅ CREDITS ⦆ </h3>

    <?php
    $fields = [
        'SONG' => 'title',
        'MUSIC & LYRICS' => 'artist',
        'ORIGINAL' => 'ogvo',
        'UTAU' => 'vb_display',
        'UST' => 'ust',
        'TUNING' => 'tuning',
        'MIX' => 'mix',
        'MOVIE' => 'movie',
        'ILLUST' => 'illust',
    ];

    echo '<ul>';
    foreach ($fields as $label => $key):
        if (!empty($cover_data[$key])): ?>
            <li> <b><?= $label ?>:</b> <?= $cover_data[$key] ?> </li>
        <?php endif;
    endforeach; ?>
    </ul>

</div>