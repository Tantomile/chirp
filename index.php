<?php
session_start();
try {
    $db = new PDO('sqlite:' . __DIR__ . '/../chirp.db');
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#0000">
    <link href="/src/styles/styles.css" rel="stylesheet">
    <link href="/src/styles/timeline.css" rel="stylesheet">
    <link href="/src/styles/menus.css" rel="stylesheet">
    <link href="/src/styles/responsive.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/@twemoji/api@latest/dist/twemoji.min.js" crossorigin="anonymous"></script>
    <!-- Cloudflare Web Analytics --><script defer src='https://static.cloudflareinsights.com/beacon.min.js' data-cf-beacon='{"token": "04bd8091c3274c64b334b30906ea3c10"}'></script><!-- End Cloudflare Web Analytics -->
    <script src="/src/scripts/general.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <title>Home - Chirp</title>
</head>

<body>
    <header>
        <div id="desktopMenu">
            <nav>
                <img src="/src/images/icons/chirp.svg" alt="Chirp" onclick="playChirpSound()">
                <a href="/" class="activeDesktop"><img src="/src/images/icons/house.svg" alt=""> Home</a>
                <a href="/discover"><img src="/src/images/icons/search.svg" alt=""> Discover</a>
                <?php if (isset($_SESSION['username'])): ?>
                <a href="/notifications"><img src="/src/images/icons/bell.svg" alt=""> Notifications</a>
                <a href="/messages"><img src="/src/images/icons/envelope.svg" alt=""> Direct Messages</a>
                <a
                    href="<?php echo isset($_SESSION['username']) ? '/user?id=' . htmlspecialchars($_SESSION['username']) : '/signin'; ?>">
                    <img src="/src/images/icons/person.svg" alt=""> Profile
                </a>
                <a href="/compose" class="newchirp">Chirp</a>
                <?php endif; ?>
            </nav>
            <div id="menuSettings">
                <?php if (isset($_SESSION['username']) && $_SESSION['username'] == 'chirp'): ?>
                <a href="/admin">🛡️ Admin panel</a>
                <?php endif; ?>
                <a href="/settings/account">⚙️ Settings</a>
                <?php if (isset($_SESSION['username'])): ?>
                <a href="/signout.php">🚪 Sign out</a>
                <?php else: ?>
                <a href="/signin/">🚪 Sign in</a>
                <?php endif; ?>
            </div>
            <!--<a href="https://sidebox.net/?ref=chirp" target="_blank"><img src="https://raw.githubusercontent.com/xkcdstickfigure/sidebox/main/banner.png" style="position: absolute; bottom: 96px; width: 256px; height: unset; margin: unset; border-radius: 8px; opacity: 0.8"></a>-->
            <button id="settingsButtonWrapper" type="button" onclick="showMenuSettings()">
                <img class="userPic"
                    src="<?php echo isset($_SESSION['profile_pic']) ? htmlspecialchars($_SESSION['profile_pic']) : '/src/images/users/guest/user.svg'; ?>"
                    alt="<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'guest'; ?>">
                <div>
                    <p class="usernameMenu">
                        <?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'Guest'; ?>
                        <?php if (isset($_SESSION['is_verified']) && $_SESSION['is_verified']): ?>
                        <img class="emoji" src="/src/images/icons/verified.svg" alt="Verified">
                        <?php endif; ?>
                    </p>
                    <p class="subText">
                        @<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'guest'; ?>
                    </p>
                </div>
                <p class="settingsButton">⚙️</p>
            </button>
        </div>
    </header>
    <main>
        <div id="feed">
            <div id="iconChirp" onclick="playChirpSound()">
                <img src="/src/images/icons/chirp.svg" alt="Chirp">
            </div>
            <div id="timelineSelect" class="extraBlur">
                <div>
                    <a id="forYou" class="selected" href="/">For you</a>
                    <a id="following" href="following">Following</a>
                </div>
            </div>
            <div id="highTraffic">
                <p></p>
            </div>
            <div id="chirps" data-offset="0">
                <div id="cookieConsent">
                    <div>
                        <p>🍪 Here, have some cookies!</p>
                        <p class="subText">Chirp uses cookies to improve your experience, to personalize content, and to
                            keep you signed in.
                            If you decline all cookies*, you can still use Chirp, but some features may not work as
                            intended.
                        </p>
                        <div>
                            <button class="button" type="button" onclick="acceptCookies()">Accept all cookies</button>
                            <button class="button following" type="button" onclick="acceptCookies()">Accept only
                                essential cookies</button>
                            <button type="button" class="button cancel" onclick="declineCookies()">Decline all
                                cookies*</button>
                        </div>
                    </div>
                </div>
                <!-- Chirps will be loaded here -->
            </div>
            <div id="noMoreChirps" style="display: none;">
                <div class="lds-ring">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
        </div>
    </main>

    <aside id="sideBar">
        <?php include 'include/sideBar.php';?>

    </aside>
    <footer>
        <div class="mobileCompose">
            <?php if (isset($_SESSION['username'])): ?>
            <a class="chirpMoile" href="/compose">Chirp</a>
            <?php endif; ?>
        </div>
        <div class="mobileMenuFooter">
            <a href="/" class="active"><img src="/src/images/icons/house.svg" alt="Home"></a>
            <a href="/discover"><img src="/src/images/icons/search.svg" alt="Discover"></a>
            <a href="/notifications"><img src="/src/images/icons/bell.svg" alt="Notifications"></a>
            <a href="/messages"><img src="/src/images/icons/envelope.svg" alt="Direct Messages"></a>
            <a
                href="<?php echo isset($_SESSION['username']) ? '/user?id=' . htmlspecialchars($_SESSION['username']) : '/signin'; ?>"><img
                    src="/src/images/icons/person.svg" alt="Profile"></a>
        </div>
    </footer>
    <script>
    let loadingChirps = false; // Flag to track if chirps are currently being loaded

    function updatePostedDates() {
        const chirps = document.querySelectorAll('.chirp .postedDate');
        chirps.forEach(function(chirp) {
            const timestamp = chirp.getAttribute('data-timestamp');
            const postDate = new Date(parseInt(timestamp) * 1000);
            const now = new Date();
            const diffInMilliseconds = now - postDate;
            const diffInSeconds = Math.floor(diffInMilliseconds / 1000);
            const diffInMinutes = Math.floor(diffInSeconds / 60);
            const diffInHours = Math.floor(diffInMinutes / 60);
            const diffInDays = Math.floor(diffInHours / 24);

            let relativeTime;

            if (diffInSeconds < 60) {
                relativeTime = diffInSeconds + "s ago";
            } else if (diffInMinutes < 60) {
                relativeTime = diffInMinutes + "m ago";
            } else if (diffInHours < 24) {
                relativeTime = diffInHours + "h ago";
            } else if (diffInDays < 7) {
                relativeTime = diffInDays + "d ago";
            } else {
                const options = {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                };
                relativeTime = postDate.toLocaleString([], options);
            }

            chirp.textContent = relativeTime;
        });
    }

    function showLoadingSpinner() {
        document.getElementById('noMoreChirps').style.display = 'block';
    }

    function hideLoadingSpinner() {
        document.getElementById('noMoreChirps').style.display = 'none';
    }

    function loadChirps() {
        if (loadingChirps) return; // If already loading, exit

        const chirpsContainer = document.getElementById('chirps');
        const offset = parseInt(chirpsContainer.getAttribute('data-offset'));

        loadingChirps = true; // Set loading flag
        showLoadingSpinner(); // Show loading spinner

        setTimeout(() => {
            fetch(`/fetch_chirps.php?offset=${offset}`)
                .then(response => response.json())
                .then(chirps => {
                    chirps.forEach(chirp => {
                        const chirpDiv = document.createElement('div');
                        chirpDiv.className = 'chirp';
                        chirpDiv.id = chirp.id;
                        chirpDiv.innerHTML = `
                        <a class="chirpClicker" href="/chirp?id=${chirp.id}">
                            <div class="chirpInfo">
                                <div>
                                    <img class="userPic"
                                        src="${chirp.profilePic ? chirp.profilePic : '/src/images/users/guest/user.svg'}"
                                        alt="${chirp.name ? chirp.name : 'Guest'}">
                                    <div>
                                        <p>${chirp.name ? chirp.name : 'Guest'}
                                            ${chirp.isVerified ? '<img class="verified" src="/src/images/icons/verified.svg" alt="Verified">' : ''}
                                        </p>
                                        <p class="subText">@${chirp.username ? chirp.username : 'guest'}</p>
                                    </div>
                                </div>
                                <div class="timestampTimeline">
                                    <p class="subText postedDate" data-timestamp="${chirp.timestamp}"></p>
                                </div>
                            </div>
                            <pre>${chirp.chirp}</pre>
                        </a>
                        <div class="chirpInteract">
                            <button type="button" class="reply" onclick="location.href='/chirp/?id=${chirp.id}'"><img alt="Reply" src="/src/images/icons/reply.svg"> <span class="reply-count">${chirp.reply_count}</span></button>
                            <a href="/chirp?id=${chirp.id}"></a>
                               <button type="button" class="rechirp" onclick="updateChirpInteraction(${chirp.id}, 'rechirp', this)"><img alt="Rechirp" src="/src/images/icons/${chirp.rechirped_by_current_user ? 'rechirped' : 'rechirp'}.svg"> <span class="rechirp-count">${chirp.rechirp_count}</span></button>
                            <a href="/chirp?id=${chirp.id}"></a>
                                 <button type="button" class="like" onclick="updateChirpInteraction(${chirp.id}, 'like', this)"><img alt="Like" src="/src/images/icons/${chirp.liked_by_current_user ? 'liked' : 'like'}.svg"> <span class="like-count">${chirp.like_count}</span></button>
                        </div>
                    `;
                        chirpsContainer.appendChild(chirpDiv);

                        // Set initial styles based on initial state
                        const likeButton = chirpDiv.querySelector('.like');
                        if (chirp.liked_by_current_user) {
                            likeButton.style.color = '#D92D20'; // Set liked color
                        }

                        const rechirpButton = chirpDiv.querySelector('.rechirp');
                        if (chirp.rechirped_by_current_user) {
                            rechirpButton.style.color = '#12B76A'; // Set rechirped color
                        }
                    });

                    chirpsContainer.setAttribute('data-offset', offset +
                        12); // Correctly increment the offset

                    updatePostedDates();
                    twemoji.parse(chirpsContainer);
                })
                .catch(error => {
                    console.error('Error fetching chirps:', error);
                })
                .finally(() => {
                    loadingChirps = false; // Reset loading flag
                    hideLoadingSpinner(); // Hide loading spinner
                });
        }, 300);
    }

    // Function to handle button click animation
    function handleButtonClick(button) {
        button.classList.add('button-clicked'); // Add the animation class
        setTimeout(() => {
            button.classList.remove('button-clicked'); // Remove the animation class after 100ms
        }, 100);
    }

    // Add event listeners to each button
    document.querySelectorAll('.reply, .rechirp, .like').forEach(button => {
        button.addEventListener('click', () => {
            handleButtonClick(button); // Call the animation function
        });
    });


    function updateChirpInteraction(chirpId, action, button) {
        fetch(`/interact_chirp.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    chirpId,
                    action
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const countElement = button.querySelector(`.${action}-count`);
                    const currentCount = parseInt(countElement.textContent.trim());

                    if (action === 'like') {
                        button.querySelector('img').src = data.like ? '/src/images/icons/liked.svg' :
                            '/src/images/icons/like.svg';
                        button.classList.toggle('liked', data.like);
                        countElement.textContent = data.like_count;
                        button.style.color = data.like ? '#D92D20' : '';
                    } else if (action === 'rechirp') {
                        button.querySelector('img').src = data.rechirp ? '/src/images/icons/rechirped.svg' :
                            '/src/images/icons/rechirp.svg';
                        button.classList.toggle('rechirped', data.rechirp); // Toggle 'rechirped' class
                        countElement.textContent = data.rechirp_count;
                        button.style.color = data.rechirp ? '#12B76A' : ''; // Set color based on rechirp status
                    }
                } else if (data.error === 'not_signed_in') {
                    window.location.href = '/signin/';
                }
            })
            .catch(error => {
                console.error('Error updating interaction:', error);
            });
    }


    loadChirps();

    window.addEventListener('scroll', () => {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight) {
            loadChirps();
        }
    });

    setInterval(updatePostedDates, 1000);



    <?php
if (isset($_SESSION['error_message'])) {
    echo 'console.error(' . json_encode($_SESSION['error_message']) . ');';
    unset($_SESSION['error_message']); // Clear the error message after displaying it
}
?>


    </script>
    
</body>

</html>