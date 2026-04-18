            </main>


        </div>

        <img src="/_assets/150.gif" class="dancy" style="position: fixed; bottom: 0; right: 0; width: 10rem; z-index: -1; pointer-events: none;"/>
        <img src="/_assets/dancy.gif" class="dancy" style="position: fixed; bottom: 0; left: 0; width: 10rem; z-index: -1; pointer-events: none;"/>
    </body>
</html>


<script>
    // JavaScript to calculate age
    function calculateAge(birthDate) {
        const today = new Date();
        const dob = new Date(birthDate);
        let age = today.getFullYear() - dob.getFullYear();
        const monthDiff = today.getMonth() - dob.getMonth();

        // Adjust if birthday hasn't happened yet this year
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        return age;
    }

    // Replace with your birthday (YYYY-MM-DD)
    document.getElementById('age').innerText = calculateAge('1998-05-26');
</script>
