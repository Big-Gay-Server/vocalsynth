<h1>【UTAU カバー】 <?= $cover_data['title'] ?> 【<?= $cover_data['voicebank'] ?>】</h1>
<br>
<div class="video-container">
    <iframe src="<?= $cover_data['embed_url'] ?>" frameborder="0" allowfullscreen loading="lazy"></iframe>
</div>
<div class='credits'>
    <h3> ⦅ CREDITS ⦆ </h3>
    <li> <b>SONG:</b> <?= $cover_data['title'] ?>
    <li> <b>MUSIC & LYRICS:</b>
        <?php
        $ml = $cover_data['raw']['music & lyrics'] ?? 'N/A';
        echo htmlspecialchars(is_array($ml) ? implode(', ', $ml) : $ml);
        ?>
    <li> <b>ORIGINAL:</b>
        <?php
        $og = $cover_data['raw']['original vocals'] ?? 'N/A';
        echo htmlspecialchars(is_array($og) ? implode(', ', $og) : $og);
        ?>
    <li> <b>UTAU:</b> <?= $cover_data['vb_display'] ?>
    <li> <b>UST:</b>
        <?php
        $ust = $cover_data['raw']['UST'] ?? 'N/A';
        echo htmlspecialchars(is_array($ust) ? implode(', ', $ust) : $ust);
        ?>
    <li> <b>TUNING:</b>
        <?php
        $tn = $cover_data['raw']['tuning'] ?? 'N/A';
        echo htmlspecialchars(is_array($tn) ? implode(', ', $tn) : $tn);
        ?>
</div>