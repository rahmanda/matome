User
----
GET user/
  [token] Get user based on user account.

POST user/
  Register new user based on user account.

POST user/login
  Log in to system and get token.

POST user/forgot
  Send password to respective email account.

POST user/reset
  Reset password to respective user account.

GET user/confirm/{code}
  Attempt account confirmation.

GET user/logout
  [token] Log out from system.

PUT user/
  [token] Update user based on user account.

GET user/{id}
  [token] Get user based on id [Admin].

PUT user/{id}
  [token] Update user based on id [Admin].

DELETE user/{id}
  [token] Delete user based on id [Admin].

Scheduler
---------
GET scheduler/
  [token] Get schedule based on user account.

POST scheduler/
  [token] Create new schedule based on user account.

PUT scheduler/
  [token] Update schedule based on user account.

GET scheduler/{id}
  [token] Get schedule by id [Admin].

PUT scheduler/{id}
  [token] Update schedule by id [Admin].

DELETE scheduler/{id}
  [token] Delete schedule by id [Admin].

Creator
-------
GET creator/
  [token] Get creator based on user account.

POST creator/
  [token] Create new creator based on user account.

PUT creator/
  [token] Update creator based on user account.

GET creator/{id}
  [token] Get creator based on id [Admin].

PUT creator/{id}
  [token] Update creator based on id [Admin].

DELETE creator/{id}
  [token] Delete creator based on id [Admin].