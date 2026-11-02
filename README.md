# 🏏 Cricket Tournament Management System (CTMS)

A web-based **Cricket Tournament Management System** developed as an academic assignment. The system focuses on managing cricket tournament information and automating live cricket match scorecard testing through probability-based match simulation.

## 📌 Project Overview

The Cricket Tournament Management System (CTMS) is designed to simplify cricket tournament and match management while reducing the time required to test live cricket scorecards.

The project is developed in two main phases:

- **Phase 01 – Live Cricket Scorecard Test Automation**
- **Phase 02 – Cricket Tournament Management System**

The first phase focuses on automatically generating cricket match events using predefined probability ratios. The second phase focuses on managing tournament-related information through a centralized database and web services.

## 🎯 Project Objectives

- Automate the testing of live cricket scorecards.
- Generate random cricket match situations using probability-based logic.
- Simulate ball-by-ball match events.
- Update the scorecard dynamically without reloading the webpage.
- Manage tournament and match-related information.
- Store match data in a centralized database for future reference.
- Provide web services for communication between the frontend and backend.

## ⚙️ Phase 01 – Live Cricket Scorecard Test Automation

The test automation module simulates a cricket match by generating random match events according to predefined probability ratios.

### 1. Probability-Based Match Simulation

The system uses probability ratios to determine possible events during a cricket delivery.

Examples of simulated events include:

- Successful and failed deliveries
- Legal and illegal deliveries
- Batting, defending, and leaving the ball
- Ball contact with the bat, leg guard, gloves, or other body parts
- Runs scored (0, 1, 2, 3, 4, 5, and 6)
- Catches and missed catches
- Bowled dismissals
- LBW decisions
- Run-outs and stumpings
- Wicketkeeper actions

### 2. Random State Generation

Random numbers are used to select an event according to its assigned probability ratio.

For example, when four bowlers have equal selection probabilities:

| Bowler | Selection Probability |
|---|---:|
| Player 01 | 25% |
| Player 02 | 25% |
| Player 03 | 25% |
| Player 04 | 25% |

The random selection mechanism chooses one bowler from the available options.

### 3. Live Scorecard Automation

The simulation generates match events automatically and updates the scorecard based on the generated results.

The scorecard includes:

- Batting information
- Bowling information
- Runs scored
- Wickets
- Overs
- Extras
- Total innings score
- Player performance details

### 4. Autoplay Functionality

The Autoplay feature starts the match simulation and continues generating events until the match reaches its completion condition.

### 5. Dynamic Scorecard Updates

AJAX is used to communicate with backend services and update the live scorecard without redirecting to another page or reloading the current page.

## 🏆 Phase 02 – Cricket Tournament Management System

The second phase extends the project into a centralized cricket tournament management application.

The system scope includes the following management areas:

- User authentication and role management
- Tournament information management
- Team and player information management
- Match management
- Live scorecard and match data management

The system uses web services to exchange information between the frontend and backend. Tournament and match records are intended to be stored in a centralized database for future reference.

## 🧠 System Logic

The match simulation follows a decision-based process.

1. Select a bowler.
2. Generate a delivery result.
3. Determine whether the delivery is legal or illegal.
4. Generate the batsman's response.
5. Determine the type of ball contact.
6. Generate runs, fielding events, or dismissal decisions.
7. Update the scorecard.
8. Continue until the innings or match is completed.

The probability values can initially be predefined. When sufficient player-specific historical data is available, the system can use calculated player statistics to determine more suitable probability ratios.

## 🔌 Web Services / API

The system uses web services to exchange data between the client and server.

Example API documentation format:

| Field | Description |
|---|---|
| Web Service Name | User Login |
| URL | `localhost/ctms/loginvalidate.php` |
| Method | POST |
| Request Format | Form Data |
| Response Format | JSON |

**Sample Request**

```text
username=asanka
password=12345678
```

**Sample JSON Response**

```json
{
  "Userid": "112",
  "UserRoleId": "2"
}
```

Other API documentation should describe the relevant service URL, request method, payload, and response format.

## 🛠️ Technologies

The project requirements and source structure involve the following technologies:

- **PHP** – Server-side development
- **JavaScript** – Client-side functionality
- **AJAX** – Asynchronous communication
- **JSON** – Data exchange between web services and the frontend
- **SQL** – Database operations
- **HTML & CSS** – Web interface development

## 📂 Main System Components

The project includes core match simulation components such as:

- `MatchEngine` – Match simulation engine
- `MatchLogic` – Decision-making and match rules
- `RandomSelector` – Random selection based on probability ratios
- API services – Communication between the frontend and backend

## 📄 System Documentation

The assignment documentation covers:

1. Use Case Diagram
2. Class Diagram
3. ER / EER Diagram
4. Web Service / API Documentation
5. Database SQL Statements
6. PHP Source Code
7. Software Testing Document

## 🧪 Testing

The test automation process is intended to verify different cricket match situations, including:

- Random player selection
- Delivery outcomes
- Runs and extras calculations
- Wicket and dismissal decisions
- Scorecard updates
- Match completion conditions
- AJAX-based updates without page reloads

## 🎓 Project Information

| Item | Details |
|---|---|
| Project Name | Cricket Tournament Management System |
| Abbreviation | CTMS |
| Project Type | University Academic Assignment |
| Main Domain | Cricket Tournament Management |
| Special Feature | Live Cricket Scorecard Test Automation |
| GitHub | [dmync](https://github.com/dmync) |

---

**Note:** This project was developed for academic purposes as part of a university assignment. The probability-based simulation is intended to support automated testing of cricket scorecard scenarios, rather than predict actual cricket match outcomes.
