<script>
		// Stop the pulse animation after 5 seconds
setTimeout(() => {
    const badges = document.querySelectorAll('.available-now');
    badges.forEach(badge => {
        badge.classList.add('animations-ended');
    });
    
}, 5000);

</script>