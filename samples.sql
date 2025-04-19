mysql> select * from TicketComments inner join Users on TicketComments.UserID = Users.ID where TicketComments.TicketID = 2 order by Time;
+----------+--------+-------------------------------------------+---------------------+----+-----------+----------+-------------+--------------------+
| TicketID | UserID | Comment                                   | Time                | ID | FirstName | LastName | AccessLevel | EmailAddress       |
+----------+--------+-------------------------------------------+---------------------+----+-----------+----------+-------------+--------------------+
|        2 |      4 | I can't figure out how to make this work. | 2025-03-26 13:03:41 |  4 | Sam       | Hill     | Client      | shill@example.com  |
|        2 |      1 | Did you reboot it?                        | 2025-03-26 13:04:09 |  1 | Jordan    | Smith    | Support     | jsmith@example.com |
|        2 |      4 | Thanks, That worked!!                     | 2025-03-26 13:04:28 |  4 | Sam       | Hill     | Client      | shill@example.com  |
|        2 |      1 | Glad that worked.  Closing Ticket.        | 2025-03-26 13:04:52 |  1 | Jordan    | Smith    | Support     | jsmith@example.com |
+----------+--------+-------------------------------------------+---------------------+----+-----------+----------+-------------+--------------------+
4 rows in set (0.00 sec)

mysql> select * from TicketUsers inner join Tickets on TicketUsers.TicketID = Tickets.ID where TicketUsers.UserID = 1;
+----------+--------+----+--------+------+
| TicketID | UserID | ID | Status | FAQ  |
+----------+--------+----+--------+------+
|        1 |      1 |  1 | Open   |    0 |
|        2 |      1 |  2 | Closed |    1 |
+----------+--------+----+--------+------+
2 rows in set (0.00 sec)

