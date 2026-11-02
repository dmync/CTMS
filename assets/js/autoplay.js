let autoTimer = null;

/* ---------- AUTO PLAY CONTROLS ---------- */
function startAutoPlay(matchId) {
  stopAutoPlay();
  document.getElementById("liveStatus").innerText = "Auto";
  document.getElementById("liveStatus").className = "badge bg-success";

  autoTimer = setInterval(() => {
    generateBall(matchId);
  }, 1200);
}

function stopAutoPlay() {
  if (autoTimer) {
    clearInterval(autoTimer);
    autoTimer = null;
  }
  document.getElementById("liveStatus").innerText = "Idle";
  document.getElementById("liveStatus").className = "badge bg-secondary";
}

/* ---------- BALL GENERATION ---------- */
function generateBall(matchId) {
  fetch("../api/generate_ball.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "match_id=" + matchId
  })
    .then(r => r.text())
    .then(txt => {
      try {
        JSON.parse(txt);
        refreshLive(matchId);
      } catch (e) {
        console.error("Invalid JSON from generate_ball.php:", txt);
        stopAutoPlay();
      }
    });
}

/* ---------- LIVE STATE ---------- */
function refreshLive(matchId) {
  fetch("../api/get_live_state.php?match_id=" + matchId)
    .then(r => r.json())
    .then(data => {
      if (data.status !== "ok") {
        console.warn("Live state error:", data);
        stopAutoPlay();
        return;
      }

      // SCORE LINE
      document.getElementById("scoreLine").innerText =
        `Innings ${data.innings_no}: ${data.score.runs}/${data.score.wickets} (${data.score.overs})`;

      // BATTING
      renderBatting(data.batting);

      // BOWLING
      renderBowling(data.bowling);

      // LAST BALLS
      renderLastBalls(data.last_balls);
    })
    .catch(err => {
      console.error("Live fetch failed", err);
      stopAutoPlay();
    });
}

/* ---------- UI RENDERERS ---------- */
function renderBatting(rows) {
  const body = document.getElementById("batBody");
  body.innerHTML = "";

  if (!rows || rows.length === 0) {
    body.innerHTML = `<tr><td colspan="6" class="text-muted">No batting data</td></tr>`;
    return;
  }

  rows.forEach(r => {
    body.innerHTML += `
      <tr>
        <td>${r.player}</td>
        <td>${r.status}</td>
        <td>${r.runs}</td>
        <td>${r.balls}</td>
        <td>${r.fours}</td>
        <td>${r.sixes}</td>
      </tr>`;
  });
}

function renderBowling(rows) {
  const body = document.getElementById("bowlBody");
  body.innerHTML = "";

  if (!rows || rows.length === 0) {
    body.innerHTML = `<tr><td colspan="4" class="text-muted">No bowling data</td></tr>`;
    return;
  }

  rows.forEach(r => {
    body.innerHTML += `
      <tr>
        <td>${r.bowler}</td>
        <td>${r.overs}</td>
        <td>${r.runs}</td>
        <td>${r.wickets}</td>
      </tr>`;
  });
}

function renderLastBalls(list) {
  const ul = document.getElementById("lastBalls");
  ul.innerHTML = "";

  if (!list || list.length === 0) {
    ul.innerHTML = `<li class="list-group-item text-muted">No balls yet</li>`;
    return;
  }

  list.forEach(txt => {
    const li = document.createElement("li");
    li.className = "list-group-item";
    li.textContent = txt;
    ul.appendChild(li);
  });
}
