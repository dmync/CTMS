# 🏏 Cricket Tournament Management System (CTMS)

## 📌 About the Project

The Cricket Tournament Management System (CTMS) is a web-based application developed to manage cricket tournaments, teams, players, matches, and live match scoring.

The system provides a centralized platform for tournament administration and real-time cricket match management. It includes match scoring functionality, player statistics, scorecards, and reporting features.

## ✨ Features

### 1. User Management
- User login and authentication
- User management

### 2. Tournament Management
- Create and manage tournaments
- View tournament information

### 3. Team Management
- Manage cricket teams
- View team details

### 4. Player Management
- Manage player information
- View player details

### 5. Match Management
- Create new cricket matches
- View match lists
- Initialize and start matches
- View match summaries

### 6. Live Match Scoring
- Live match score updates
- Ball and delivery generation
- Batting statistics
- Bowling statistics
- Live match state tracking
- Detailed match scorecards

### 7. Reports
- Generate and view cricket-related reports
- Access match and player statistics

## 🛠️ Technologies Used

- **Backend:** PHP
- **Frontend:** HTML, CSS, JavaScript
- **Database:** MySQL
- **Styling:** CSS3
- **Development Environment:** Apache / XAMPP

## 📂 Project Structure

```text
CTMS/
│
├── api/
│   ├── bootstrap.php
│   ├── generate_ball.php
│   ├── generate_delivery.php
│   ├── get_batting.php
│   ├── get_bowling.php
│   ├── get_live_state.php
│   ├── get_scorecard.php
│   ├── init_match.php
│   ├── login.php
│   ├── logout.php
│   ├── start_match.php
│   └── update_score.php
│
├── assets/
│   ├── css/
│   │   ├── app.css
│   │   └── style.css
│   │
│   └── js/
│       ├── autoplay.js
│       └── theme.js
│
├── config/
│   └── db.php
│
├── core/
│   ├── Auth.php
│   ├── MatchEngine.php
│   ├── MatchLogic.php
│   └── RandomSelector.php
│
├── pages/
│   ├── create_match.php
│   ├── dashboard.php
│   ├── live_match.php
│   ├── login.php
│   ├── match_list.php
│   ├── match_summary.php
│   ├── players.php
│   ├── reports.php
│   ├── teams.php
│   ├── tournaments.php
│   ├── users.php
│   │
│   └── partials/
│       ├── layout_bottom.php
│       ├── layout_top.php
│       └── nav.php
│
├── sql/
│   └── ctms.sql
│
├── index.php
└── README.md
```

## ⚙️ Installation and Setup

### Prerequisites

- XAMPP or another PHP development environment
- PHP
- MySQL
- Web Browser

### Step 1: Clone the Repository

```bash
git clone https://github.com/dmync/CTMS.git
```

### Step 2: Move the Project

Copy the project folder into the XAMPP `htdocs` directory.

### Step 3: Start XAMPP

Start the following services:

- Apache
- MySQL

### Step 4: Import the Database

1. Open phpMyAdmin.
2. Create a new MySQL database.
3. Select the database.
4. Import the `sql/ctms.sql` file.

### Step 5: Configure Database Connection

Open `config/db.php` and update the database connection settings according to your local environment.

### Step 6: Run the Application

Open your browser and visit:

```text
http://localhost/CTMS/
```

## 🎯 Project Purpose

This project was developed to apply web development and database management concepts in a practical cricket tournament management application.

It demonstrates the use of PHP, MySQL, JavaScript, and structured application development.

## 👩‍💻 Author

**Developed by:** dmync

**GitHub Repository:**  
https://github.com/dmync/CTMS
