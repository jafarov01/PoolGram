# PoolGram
PoolGram PHP project.

This solution is a web application where logged-in users can cast votes on polls and admin users can manage polls.

Features:

Users can view, vote on, and create polls.
Polls have a question, options, deadline, and creation time.
Users can only vote once per poll.
Admin users can create, edit, and delete polls.
Polls can be open or closed (expired).
Closed polls display results.
Getting Started:

Clone this repository.
Create a database and configure connection details.
Run the application.
Detailed Description:

Homepage:

Displays all polls, open and closed, with basic information.
Open polls are listed at the top, sorted by creation date (newest first).
Closed polls are listed below, also sorted by creation date.
Clicking a poll button redirects to the voting page.
Voting Page:

Shows the poll details (question, options, deadline).
Allows users to vote for one or more options (depending on poll).
Displays a success message on successful vote and an error message if no option is selected.
Poll Creation:

Accessible only to admin users.
Allows creating a new poll with:
Question
Options (textarea, one option per line)
Multiple selection (radio button)
Voting deadline (date)
Authentication:

Users can register with username, email, and password.
Login functionality with username and password.
Admin user has special privileges for managing polls.
