# Dice Game API

## Description
This API manages a dice game application where users can register, log in, play dice games, view their game history, and administrators can perform various operations related to player management and statistics.

## Authentication
- Authentication is required for most endpoints.
- Passport is used for authentication.

## Authorization
- Different routes are accessible based on the user's role:
  - Players: Can access routes related to playing games and viewing their game history.
  - Administrators: Can access routes related to player management and statistics.

## Endpoints

### Open Routes (No Authentication Required)
- `POST /api/v1/players`: Register a new player.
- `POST /api/v1/players/login`: Log in with credentials.

### Authenticated Routes 
- `PUT /api/v1/players/{id}`: Change the name of the player.
- `POST /api/v1/players/{id}/logout`: Log out.

### Authenticated Routes (Player Role)
- `POST /api/v1/players/{id}/games`: Record a dice roll for a specific player.
- `GET /api/v1/players/{id}/games`: Get a list of game records for a specific player.
- `DELETE /api/v1/players/{id}/games`: Delete all game records for a player.

### Authenticated Routes (Admin Role)
- `GET /api/v1/players`: Get a list of all players in the system.
- `GET /api/v1/players/ranking`: Get the average ranking of all players in the system.
- `GET /api/v1/players/ranking/winner`: Get the player with the highest success rate.
- `GET /api/v1/players/ranking/loser`: Get the player with the lowest success rate.

## Security
- Authentication: Passport is used for authentication.
- Role-Based Access Control (RBAC): Different routes are accessible based on the user's role.

## Testing
- Unit and integration tests are implemented using PHPUnit.
- Test-Driven Development (TDD) approach is followed to ensure the functionality of each route.

For detailed information on request and response formats, please refer to the Swagger documentation or the API codebase.
