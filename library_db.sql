-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 30, 2025 at 05:27 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(100) NOT NULL,
  `isbn` varchar(13) NOT NULL,
  `published_year` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `available_copies` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `isbn`, `published_year`, `image_url`, `available_copies`) VALUES
(1, 'The Hitchhiker\'s Guide to the Galaxy', 'Douglas Adams', '0-345-39180-2', 1979, 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1586548540i/386162.jpg', 1),
(2, 'Dune', 'Frank Herbert', '0-441-17271-7', 1965, 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1589134015i/44767458.jpg', 1),
(3, 'Project Hail Mary', 'Andy Weir', '0-593-13490-3', 2021, 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1601618386i/54493401.jpg', 1),
(4, 'The Martian', 'Andy Weir', '0-553-41802-5', 2011, 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1559483320i/18066538.jpg', 1),
(5, 'The Lord of the Rings', 'J.R.R. Tolkien', '0-618-05116-1', 1954, 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1566425108i/33.jpg', 1),
(6, 'A Game of Thrones', 'George R.R. Martin', '0-553-59371-4', 1996, 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1436732693i/13496.jpg', 1),
(7, 'The Chronicles of Narnia', 'C.S. Lewis', '0-06-440499-1', 1950, 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1352467265i/11127.jpg', 1),
(8, 'Harry Potter and the Sorcerer\'s Stone', 'J.K. Rowling', '0-590-35340-3', 1997, 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1474154022i/3.jpg', 0),
(9, 'The Hunger Games', 'Suzanne Collins', '0-439-02348-3', 2008, 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1586722975i/2767052.jpg', 1),
(10, 'To Kill a Mockingbird', 'Harper Lee', '0-446-31078-6', 1960, 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1553383690i/2657.jpg', 1),
(11, 'Pride and Prejudice', 'Jane Austen', '0-679-40542-8', 1813, 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1320399351i/184428.jpg', 1),
(12, '1984', 'George Orwell', '0-452-28423-6', 1949, 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1532714506i/40961427.jpg', 1),
(13, 'Brave New World', 'Aldous Huxley', '0-06-085052-3', 1932, 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1575512969i/5129.jpg', 0),
(14, 'The Great Gatsby', 'F. Scott Fitzgerald', '0-7432-7356-7', 1925, 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1490528560i/4671.jpg', 1),
(15, 'Moby-Dick', 'Herman Melville', '0-14-243724-4', 1851, 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1327940733i/15372.jpg', 1),
(16, 'The Catcher in the Rye', 'J.D. Salinger', '0-316-76948-7', 1951, 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1398034300i/5107.jpg', 0),
(17, 'One Hundred Years of Solitude', 'Gabriel García Márquez', '0-06-112008-1', 1967, 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1579568916i/320.jpg', 1),
(18, 'The Alchemist', 'Paulo Coelho', '0-06-112241-6', 1988, 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1654522851i/865.jpg', 1),
(19, 'The Hobbit', 'J.R.R. Tolkien', '0-618-05116-4', 1937, 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1546071217i/5907.jpg', 1),
(20, 'Frankenstein', 'Mary Shelley', '0-486-28211-2', 1818, 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1672323381i/18490.jpg', 0),
(26, 'Rich Dad Poor Dad', 'Robert T. Kiyosaki', '9999', 1997, 'https://cdn.kobo.com/book-images/c81ea4de-cfb7-415d-8634-314aad041fdb/1200/1200/False/rich-dad-poor-dad-9.jpg', 1),
(27, 'Atomic Habits', 'James Clear', '4444', 1990, 'https://tse4.mm.bing.net/th/id/OIP.1-GY-OS39OGxnj-sAXKg0AHaJK?rs=1&amp;pid=ImgDetMain&amp;o=7&amp;rm=3', 1),
(28, 'Three men in a boat', 'Jerome K Jerome', '345626', 1975, 'https://m.media-amazon.com/images/I/91euE2bGQYL._AC_SL1500_.jpg', 1),
(29, '5 am Club', 'Robin Sharma', '978-938906361', 2018, 'https://www.bing.com/images/search?view=detailV2&amp;id=1401A7398AB11201C4CE4744ADEEAD15A222C693&amp;thid=OIP.-FkAx5gsmOcOMEhkIHblMgHaLU&amp;mediaurl=https%3a%2f%2fm.media-amazon.com%2fimages%2fI%2f712VrOZ60zL.jpg&amp;exph=2294&amp;expw=1500&amp;q=5+am+cl', 1);

-- --------------------------------------------------------

--
-- Table structure for table `borrowings`
--

CREATE TABLE `borrowings` (
  `borrow_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `borrow_date` date NOT NULL,
  `return_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowings`
--

INSERT INTO `borrowings` (`borrow_id`, `book_id`, `user_id`, `borrow_date`, `return_date`) VALUES
(1, 9, 2, '2025-09-03', '2025-09-03'),
(2, 5, 2, '2025-09-03', '2025-09-12'),
(3, 11, 2, '2025-09-03', '2025-09-03'),
(4, 6, 2, '2025-09-03', '2025-09-03'),
(5, 6, 2, '2025-09-03', '2025-09-03'),
(6, 12, 2, '2025-09-03', '2025-09-03'),
(8, 16, 2, '2025-09-07', NULL),
(10, 27, 2, '2025-09-12', '2025-09-12'),
(11, 6, 2, '2025-09-12', '2025-09-12'),
(13, 6, 2, '2025-09-12', '2025-09-12'),
(14, 27, 2, '2025-09-12', '2025-09-12'),
(15, 13, 2, '2025-09-12', '2025-09-12'),
(16, 13, 2, '2025-09-12', NULL),
(17, 12, 2, '2025-09-12', '2025-09-12'),
(18, 20, 2, '2025-09-12', NULL),
(19, 8, 2, '2025-09-12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', 'admin123', 'admin'),
(2, 'user', 'user123', 'user'),
(3, 'Prathmesh', 'Prathmesh@12345', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isbn` (`isbn`);

--
-- Indexes for table `borrowings`
--
ALTER TABLE `borrowings`
  ADD PRIMARY KEY (`borrow_id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `borrowings`
--
ALTER TABLE `borrowings`
  MODIFY `borrow_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrowings`
--
ALTER TABLE `borrowings`
  ADD CONSTRAINT `borrowings_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
  ADD CONSTRAINT `borrowings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
