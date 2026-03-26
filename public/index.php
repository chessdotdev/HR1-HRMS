<?php
require_once '../modules/Recruitment.php';
$recruitment = new Recruitment();
$stmt = $recruitment->getOpenJobs();
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include '../includes/header.php'; ?>

<style>
 @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Lora:wght@400;600&display=swap');

body {
    background-color: #fafaf8;
    font-family: 'Inter', sans-serif;
    color: #1a1a1a;
}

.navbar {
    background: #ffffff !important;
    border-bottom: 1px solid #e8e8e4;
    padding: 1rem 0;
}
.navbar-brand {
    font-family: 'Lora', serif;
    font-size: 1.1rem;
    color: #1a1a1a !important;
    letter-spacing: 0.01em;
}
.nav-link { color: #555 !important; font-size: 0.875rem; }
.nav-link:hover { color: #1a1a1a !important; }

.hero-banner {
    background: #ffffff;
    border-bottom: 1px solid #e8e8e4;
    padding: 6rem 0 5rem;
    text-align: center;
}

.hero-eyebrow {
    font-size: 0.72rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: #999;
    margin-bottom: 1.25rem;
}

.hero-title {
    font-family: 'Lora', serif;
    font-size: clamp(2.25rem, 5vw, 3.5rem);
    font-weight: 600;
    color: #1a1a1a;
    line-height: 1.2;
    margin-bottom: 1.25rem;
}

.hero-subtitle {
    font-size: 1rem;
    color: #888;
    line-height: 1.75;
    max-width: 480px;
    margin: 0 auto 2.5rem;
}

.btn-explore {
    display: inline-block;
    padding: 0.7rem 2rem;
    background: #1a1a1a;
    color: #ffffff;
    font-size: 0.78rem;
    font-weight: 500;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    text-decoration: none;
    border-radius: 2px;
    transition: background 0.2s;
}
.btn-explore:hover { background: #333; color: #fff; }

.jobs-section { padding: 5rem 0; }

.section-header { margin-bottom: 3.5rem; }

.section-eyebrow {
    font-size: 0.7rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: #aaa;
    margin-bottom: 0.75rem;
}

.section-title {
    font-size: 1.75rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 0.5rem;
}

.section-desc {
    font-size: 0.875rem;
    color: #999;
    font-weight: 300;
}

.job-card {
    background: #ffffff;
    border: 1px solid #e8e8e4 !important;
    border-radius: 4px !important;
    transition: box-shadow 0.2s, border-color 0.2s;
    height: 100%;
}

.job-card:hover {
    border-color: #ccc !important;
    box-shadow: 0 8px 32px rgba(0,0,0,0.07) !important;
}

.job-card-body {
    padding: 1.75rem;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.job-dept {
    font-size: 0.68rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: #aaa;
    margin-bottom: 0.75rem;
}

.job-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 0.4rem;
    line-height: 1.3;
}

.job-location {
    font-size: 0.78rem;
    color: #bbb;
    margin-bottom: 1.5rem;
}

.card-divider {
    height: 1px;
    background: #f0f0ec;
    margin: 1.25rem 0;
}

.job-section-label {
    font-size: 0.85rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    margin-bottom: 0.6rem;
    font-weight: bold;
}

.job-list {
    list-style: none;
    padding: 0;
    margin: 0 0 1rem;
}

.job-list li {
    font-size: 0.82rem;
    padding: 0.22rem 0 0.22rem 1rem;
    position: relative;
    line-height: 1.5;
}

.job-list li::before {
    content: '·';
    position: absolute;
    left: 0.25rem;
    color: #ccc;
    font-size: 1.1rem;
    line-height: 1.2;
}

.btn-apply {
    display: block;
    width: 100%;
    padding: 0.7rem;
    background: #1a1a1a;
    border: none;
    color: #ffffff;
    font-size: 0.75rem;
    font-weight: 500;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    text-align: center;
    text-decoration: none;
    border-radius: 2px;
    transition: background 0.2s;
    margin-top: auto;
}

.btn-apply:hover { background: #333; color: #fff; }

.empty-state { text-align: center; padding: 5rem 0; }
.empty-icon { font-size: 2.5rem; opacity: 0.2; margin-bottom: 1rem; }
.empty-title { font-family: 'Lora', serif; font-size: 1.35rem; color: #bbb; margin-bottom: 0.4rem; }
.empty-desc { font-size: 0.82rem; color: #ccc; }

#jobs { scroll-margin-top: 80px; }

#chatbot-toggle {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #1a1a1a;
    color: #fff;
    width: 55px;
    height: 55px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    cursor: pointer;
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    z-index: 9999;
}

#chatbot-box {
    position: fixed;
    bottom: 85px;
    right: 20px;
    width: 320px;
    background: #fff;
    border: 1px solid #e8e8e4;
    border-radius: 6px;
    display: none;
    flex-direction: column;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    overflow: hidden;
    z-index: 9999;
    transform: translateY(40px); /* start lower */
    transition: all 0.3s ease;
}

#chatbot-header {
    background: #1a1a1a;
    color: #fff;
    padding: 12px;
    font-size: 0.85rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

#chatbot-close {
    background: none;
    border: none;
    color: #fff;
    font-size: 16px;
    cursor: pointer;
}

#chatbot-messages {
    padding: 12px;
    height: 280px;
    overflow-y: auto;
    font-size: 0.8rem;
    overflow-y: auto; 
    scrollbar-width: thin;
    scrollbar-color: black transparent;
}
#chatbot-messages::-webkit-scrollbar {
    width: 6px;
}
#chatbot-messages::-webkit-scrollbar-track {
    background: transparent;
}
#chatbot-messages::-webkit-scrollbar-thumb {
    background-color: #555;      
    border-radius: 10px;        
    border: 1px solid transparent;  
}

#chatbot-messages::-webkit-scrollbar-thumb:hover {
    background-color: #333;      
}

.chat-msg {
    margin-bottom: 8px;
    padding: 8px 10px;
    border-radius: 4px;
    max-width: 80%;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

.user-msg {
    background: #1a1a1a;
    color: #fff;
    margin-left: auto;
}
.typing-msg {
    background: transparent;   
    color: #aaa;              
    font-style: italic;
    font-size: 0.65rem;
    padding: 4px 8px;
    opacity: 0.8;
}
.bot-msg {
    background: #f0f0ec;
    color: #333;
}
.bot-msg a {
    word-break: break-all;
    overflow-wrap: anywhere;
}
#chatbot-input-area {
    display: flex;
    border-top: 1px solid #eee;
}

#chatbot-input {
    flex: 1;
    border: none;
    padding: 10px;
    font-size: 0.8rem;
}

#chatbot-input:focus {
    outline: none;
}

#chatbot-send {
    background: #1a1a1a;
    color: #fff;
    border: none;
    padding: 0 14px;
    cursor: pointer;
}
</style>
<div id="chatbot-toggle">💬</div>
<div id="chatbot-box">
    <div id="chatbot-header">
        <span>TechnoVista Chatbot Assistant</span>
        <button id="chatbot-close">&times;</button>
    </div>

    <div id="chatbot-messages"></div>

    <div id="chatbot-input-area">
        <input type="text" id="chatbot-input" placeholder="Type a message...">
        <button id="chatbot-send">➤</button>
    </div>
</div>
<!-- Hero -->
<div class="hero-banner">
    <div class="container">
        <p class="hero-eyebrow">Careers at Hotel &amp; Restaurant</p>
        <h1 class="hero-title">Find Your Place<br>With Our Team</h1>
        <p class="hero-subtitle text-dark">
            We're looking for passionate people who take pride in delivering exceptional hospitality experiences.
        </p>
        <a href="#jobs" class="btn-explore">View Open Positions</a>
    </div>
</div>

<!-- Jobs -->
<div class="jobs-section" id="jobs">
    <div class="container">
        <div class="section-header">
            <p class="section-eyebrow">Current Openings</p>
            <h2 class="section-title">Available Positions</h2>
            <p class="section-desc">Join a team that values craft, care, and genuine hospitality.</p>
        </div>

        <?php if (count($jobs) > 0): ?>
            <div class="row g-4">
                <?php foreach ($jobs as $job): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card job-card border-0">
                            <div class="job-card-body">
                                <div class="job-dept text-dark"><?= htmlspecialchars($job['department']) ?></div>
                                <h5 class="job-title"><?= htmlspecialchars($job['title']) ?></h5>

                                <?php if (!empty($job['location'])): ?>
                                    <div class="job-location text-dark">📍 <?= htmlspecialchars($job['location']) ?></div>
                                <?php endif; ?>

                                <?php
                                $reqs = array_filter(array_map('trim', explode("\n", $job['qualifications'])));
                                ?>

                                <div class="job-section-label">Responsibilities</div>
                                <ul class="job-list">
                                    <?php foreach (array_slice($reqs, 0, 3) as $req): ?>
                                        <li><?= htmlspecialchars($req) ?></li>
                                    <?php endforeach; ?>
                                </ul>

                                <div class="job-section-label">Qualifications</div>
                                <ul class="job-list">
                                    <?php foreach (array_slice($reqs, 0, 3) as $req): ?>
                                        <li><?= htmlspecialchars($req) ?></li>
                                    <?php endforeach; ?>
                                </ul>

                                <?php if (!empty($job['benefits'])): ?>
                                    <div class="card-divider"></div>
                                    <div class="job-section-label fw-bold">Benefits</div>
                                    <ul class="job-list">
                                        <?php
                                        $benefits = array_filter(array_map('trim', explode("\n", $job['benefits'])));
                                        foreach ($benefits as $benefit):
                                        ?>
                                            <li><?= htmlspecialchars($benefit) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>

                                <div class="card-divider"></div>
                                <a href="apply.php?job_id=<?= $job['id'] ?>&title=<?= urlencode($job['title']) ?>"
                                   class="btn-apply">Apply Now</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <h3 class="empty-title">No openings right now</h3>
                <p class="empty-desc">Please check back later.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
const toggleBtn = document.getElementById('chatbot-toggle');
const chatBox = document.getElementById('chatbot-box');
const closeBtn = document.getElementById('chatbot-close');
const input = document.getElementById('chatbot-input');
const sendBtn = document.getElementById('chatbot-send');
const messages = document.getElementById('chatbot-messages');

let isOpen = false;
chatBox.style.display = 'flex';
chatBox.style.opacity = '0';
chatBox.style.transform = 'translateY(20px) scale(0.95)';
chatBox.style.pointerEvents = 'none';

function toggleChat(){
    if(isOpen){
        chatBox.style.opacity = '0';
        chatBox.style.transform = 'translateY(40px) scale(0.95)';
        chatBox.style.pointerEvents = 'none';

        setTimeout(()=>{
            chatBox.style.display = "none"
        },300)

        isOpen = false;
    }else{
       
        chatBox.style.display = "flex"

        setTimeout(()=>{
            chatBox.style.display = 'flex';
            chatBox.style.opacity = '1';
            chatBox.style.transform = 'translateY(0) scale(1)';
            chatBox.style.pointerEvents = 'auto';
        },300)

        isOpen = true;
    }
}

function linkify(text) {
    return text.replace(/(https?:\/\/[^\s<]+?)([.,!?;:)\]'"]*(?:\s|$))/g, '<a href="$1" target="_blank">$1</a>$2');
}

async function sendMessage() {
    const text = input.value.trim();
    if (!text) return;

    const userMsg = document.createElement('div');
    userMsg.className = 'chat-msg user-msg';
    userMsg.textContent = text;
    const typingMsg = document.createElement('div');
    typingMsg.className = 'chat-msg bot-msg';
    typingMsg.textContent = 'Typing...';
    messages.appendChild(userMsg);
    messages.appendChild(typingMsg);
    messages.scrollTop = messages.scrollHeight;

    input.value = '';

    try {
        const res = await fetch('http://localhost:3000/chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: text })
        });

        const data = await res.json();

        const botMsg = document.createElement('div');
        botMsg.className = 'chat-msg bot-msg';
        botMsg.textContent = data.response;
        linkify(botMsg.innerHTML = linkify(botMsg.textContent));
        if(res.status === 429){
            botMsg.textContent = data.error;
        }
        
        typingMsg.remove();
        messages.appendChild(botMsg);
        messages.scrollTop = messages.scrollHeight;

    } catch (err) {
        console.error(err);
        typingMsg.remove();
        const botMsg = document.createElement('div');
        botMsg.className = 'chat-msg bot-msg';
        botMsg.textContent = 'Failed to reach the server.';
        messages.appendChild(botMsg);
        messages.scrollTop = messages.scrollHeight;
    }
}

sendBtn.addEventListener('click', sendMessage);

input.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') sendMessage();
});

toggleBtn.addEventListener('click', toggleChat);
closeBtn.addEventListener('click', toggleChat);
</script>
<?php include '../includes/footer.php'; ?>