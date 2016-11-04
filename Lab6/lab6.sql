CREATE TABLE users(userName VARCHAR(255), Password VARCHAR(255), Email VARCHAR(255), Phone VARCHAR(10), Librarian TINYINT, FirstName VARCHAR(255), LastName VARCHAR(255));
CREATE TABLE books(BookId INT(10) AUTO_INCREMENT, BookTitle VARCHAR(255), Author VARCHAR(255), Availability TINYINT DEFAULT 1, PRIMARY KEY(BookId));
CREATE TABLE loanhistory(id INT(10) AUTO_INCREMENT, Username VARCHAR(255), BookId INT(10), DueDate DATE, ReturnedDate DATE, PRIMARY KEY(id));
CREATE TABLE shelves(ShelfId INT(10), ShelfName VARCHAR(255), PRIMARY KEY(ShelfId));
CREATE TABLE booklocations(BookId INT(10), ShelfId INT(10));
INSERT INTO shelves SET ShelfId = 0, ShelfName = 'Art';
INSERT INTO shelves SET ShelfId = 1, ShelfName = 'Science';
INSERT INTO shelves SET ShelfId = 2, ShelfName = 'Sports';
INSERT INTO shelves SET ShelfId = 3, ShelfName = 'Literature';