<h1>UST/MIDI Commissions</h1>
<div class="row">
    <div class="column flex50">
        <h2>Terms</h2>
        <p>
            <center>
                <b>Do:</b><br>
                Most any song!<br><br>

                <b>Don't:</b><br>
                Rapping/Talking parts<br><br>

                <b>Notes</b><br>
                I reserve the right to refuse a commission for any reason.<br>
                USTs are <b>UNTUNED</b>. For tuning, see "UTAU Tuning and Cover Commissions" below.<br><br>

                <b>Refunds</b><br>
                Refunds are available, depending on how much of the MIDI or UST has been completed. i.e:<br>
                25% of UST done - <b>75%</b> refund<br>
                50% of UST done - <b>50%</b> refund
            </center>
        </p>

        <h2>Pricing</h2>
        <p>
            <center>
                <b>MIDI ONLY</b><br>
                1 main vocal track + 1 harmony track - 5$ /minute<br>
                Extra tracks (+harmonies, +singers) - +1$ /minute per track<br><br>

                <b>MIDI + UST</b><br>
                1 main vocal track + 1 harmony track - 7$ /minute<br>
                Extra tracks (+harmonies, +singers) - +2$ /minute per track<br><br>
            </center>
        </p>
    </div>
    <div class="column flex50">
        <h2>Examples</h2>
        <div class="video-wrapper">
            <iframe width="100%" height="100%" style="aspect-ratio: 16/9;"
                src="https://www.youtube.com/embed/jWCoWeCLC-o?autoplay=1" frameborder="0"
                allowfullscreen></iframe>
        </div><br>
        <div class="video-wrapper">
            <iframe width="100%" height="100%" style="aspect-ratio: 16/9;"
                src="http://www.youtube.com/embed/ChQT_lCH4ng?autoplay=1" frameborder="0"
                allowfullscreen></iframe>
        </div>
    </div>


</div>


    <center>Commissions for MIDIs and USTs are almost always open!<br>Just fill out the form below, and
        I'll
        get
        back to you to confirm payment and start the commission!<br><br>
        <form method="POST" id="my-form"
            action="https://script.google.com/macros/s/AKfycbxBq5oCHrlf3HfXzeUMOynpd7lg-MZFg5MTjsfaA8sSmPSGjyvk7CwrKhUzm0a1fuqrCQ/exec">
            
            <input name="Username (include site it is from)" type="text"
                placeholder="Username (include platform)" required><br><br>
            <input name="Paypal Email" type="email" placeholder="Paypal Email" required><br><br>
            <input type="radio" id="MIDI Only" name="Type of Commission" value="MIDI Only">
            <label for="MIDI Only">MIDI Only</label><br>
            <input type="radio" id="MIDI + UST" name="Type of Commission" value="MIDI + UST">
            <label for="MIDI + UST">MIDI + UST</label><br><br>
            <input name="Name of Song" type="text" placeholder="Name of Song" required><br><br>
            <input name="Link to Song" type="text" placeholder="Link to Song" required><br><br>
            <button type="submit">Send</button>
        </form>
        <script>
            window.addEventListener("load", function () {
                const form = document.getElementById('my-form');
                form.addEventListener("submit", function (e) {
                    e.preventDefault();
                    const data = new FormData(form);
                    const action = e.target.action;
                    fetch(action, {
                        method: 'POST',
                        body: data,
                    })
                        .then(() => {
                            alert("Commission Request has been received! Thank you so much! 🌙");
                        })
                });
            });

        </script>
    </center><br><br>


<hr>

<h1>UTAU Tuning and Cover Commissions</h1>
<div class="row">
    <div class="column flex50">
        <h2>Examples</h2>
        <div class="video-wrapper">
            <iframe width="100%" height="100%" style="aspect-ratio: 16/9;"
                src="https://www.youtube.com/embed/RqKRsieXYME?autoplay=1" frameborder="0"
                allowfullscreen></iframe>
        </div>
        <center>Raw Tuning - <audio controls>
                <source src="monitoring tuning raw.wav" type="audio/wav">
            </audio></center><br><br>
        <div class="video-wrapper">
            <iframe width="100%" height="100%" style="aspect-ratio: 16/9;"
                src="http://www.youtube.com/embed/oXEbyhQaX74?autoplay=1" frameborder="0"
                allowfullscreen></iframe>
        </div>
        <center>Raw Tuning - <audio controls>
                <source src="hope tuning raw.wav" type="audio/wav">
            </audio></center><br>
    </div>

    <div class="column flex50">
        <h2>Terms</h2>


        <h2>Pricing</h2>
    </div>

</div>
<br>
<center>Commissions for UTAU Tuning and Covers are taken on a case-by-case basis.<br>You can contact me on
    any of my <a href="http://lunarconstruct.net/socials">socials</a> and we can talk further about starting
    a commission!<br>
</center><br>


<hr>


<h1>oto.ini Commissions</h1>
<div class="row">
    <div class="column flex50">
        <center>
            <h2>Terms</h2>

            <b>Notes</b><br>
            Currently, I am only doing Japanese otos.<br>
            I reserve the right to refuse a commission for any reason.<br><br>


            <b>Refunds</b><br>
            Refunds are available, depending on how much of the oto has been completed. i.e:<br>
            25% of oto done - <b>75%</b> refund<br>
            50% of oto done - <b>50%</b> refund


            <h2>Pricing</h2>
            <b>Japanese CV</b><br>5$ for initial pitch, +3$ per extra pitch<br><br>
            <b>Japanese CVVC</b><br>7$ for initial pitch, +4$ per extra pitch<br><br>
            <b>Japanese VCV</b><br>10$ for initial pitch, +5$ per extra pitch<br><br>
            <b>Japanese VCV+CV</b><br>15$ for initial pitch, +8$ per extra pitch<br><br>
            <b>Japanese VCV+CVVC</b><br>17$ for initial pitch, +9$ per extra pitch<br><br>
        </center>

    </div>

    <div class="column flex50">
        <h2>Examples</h2>
        <center><a href="/vocalsynth/voicebanks/merisdae"><img
                    src="../voicebanks/merisdae/preview inactive.png" class="vbpreview"
                    onmouseover="this.src='../voicebanks/merisdae/preview.png';"
                    onmouseout="this.src='../voicebanks/merisdae/preview inactive.png'"></a>
            <a href="/vocalsynth/voicebanks/canele"><img src="../voicebanks/canele/preview inactive.png"
                    class="vbpreview" onmouseover="this.src='../voicebanks/canele/preview.png';"
                    onmouseout="this.src='../voicebanks/canele/preview inactive.png'"></a>
        </center>
    </div>

</div>