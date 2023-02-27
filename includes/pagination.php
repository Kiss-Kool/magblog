    <?php echo '<div class="pagination">';
for ($i = 1; $i <= $nombre_total_pages; $i++) {
    if ($i === $page_courante) {
        echo '<span>' . $i . '</span>';
    } else {
        echo '<a href="index.php?page=' . $i . '">' . $i . '</a>';
    }
}
echo '</div>'; ?>
