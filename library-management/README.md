# Routes Documentation

## Prithvi Narayan Campus Library Management System

---

## Public Routes

### `GET /php/setup.php`
Initialize the database and create default admin user.
- **Purpose**: One-time database setup
- **Creates**: `users` and `books` tables
- **Default User**: `admin` / `admin123`
- **Redirects to**: Login page after setup

### `GET /php/index.php` (Login Page)
Display the login form with campus branding.
- **Purpose**: User authentication
- **Method**: `POST` for form submission
- **On Success**: Redirects to dashboard
- **On Failure**: Shows error message

### `POST /php/index.php`
Process login credentials.
- **Parameters**: `username`, `password`
- **Validates**: Against users table
- **Success**: Creates session, redirects to dashboard
- **Failure**: Returns to login with error

---

## Protected Routes (Requires Login)

### `GET /php/dashboard.php`
Main dashboard showing all books and statistics.
- **Purpose**: Overview of library inventory
- **Displays**:
  - Total books count
  - Available books count
  - Borrowed books count
- **Features**:
  - Search by title, author, ISBN
  - Filter by category
  - Filter by status (available/borrowed)
- **Actions**: Add book, Edit, Delete, Borrow, Return

### `GET /php/available.php`
View all available (not borrowed) books.
- **Purpose**: Quick view of borrowable books
- **Features**:
  - Search functionality
  - Category filter
  - Direct borrow link
- **Access**: Requires login

### `GET /php/add_book.php` (Display Form)
Display form to add a new book.
- **Purpose**: Add new book to catalog
- **Fields**:
  - Title (required)
  - Author (required)
  - ISBN (optional)
  - Category (optional)
  - Cover Image URL (optional)

### `POST /php/add_book.php`
Process new book submission.
- **Validation**: Title and Author required
- **Success**: Redirects to dashboard with success message
- **Failure**: Returns form with error message

### `GET /php/edit_book.php?id={book_id}`
Display form to edit existing book.
- **Purpose**: Update book information
- **Pre-fills**: Existing book data
- **Fields**: Same as add form

### `POST /php/edit_book.php?id={book_id}`
Process book update.
- **Validation**: Title and Author required
- **Success**: Redirects to dashboard with success message

### `GET /php/borrow.php?id={book_id}`
Display borrow form for a specific book.
- **Purpose**: Record book borrowing
- **Shows**: Book details
- **Form**: Borrower name input

### `POST /php/borrow.php?id={book_id}`
Process book borrowing.
- **Updates**: Book status to "borrowed"
- **Records**: Borrower name and date
- **Success**: Redirects to dashboard

### `GET /php/return.php?id={book_id}`
Return a borrowed book.
- **Purpose**: Mark book as available again
- **Updates**: Clears borrower info
- **Redirects**: Back to dashboard

### `GET /php/delete.php?id={book_id}`
Delete a book from the system.
- **Purpose**: Remove book permanently
- **Confirmation**: JavaScript confirm dialog
- **Redirects**: Back to dashboard

### `GET /php/logout.php`
End user session.
- **Purpose**: Logout user
- **Action**: Destroys session
- **Redirects**: To login page

---

## Query Parameters

### Dashboard Filters
| Parameter | Type | Description |
|-----------|------|-------------|
| `search` | string | Search term for title/author/ISBN |
| `category` | string | Filter by category |
| `status` | string | Filter by status: `available` or `borrowed` |

### Success Messages
The dashboard accepts `success` query parameter to display messages:
| Value | Message |
|-------|---------|
| `added` | Book added successfully |
| `updated` | Book updated successfully |
| `borrowed` | Book borrowed successfully |
| `returned` | Book returned successfully |
| `deleted` | Book deleted successfully |

---

## Session Requirements

All protected routes check for:
```php
$_SESSION['user_id']
$_SESSION['username']
```

If not set, user is redirected to login page.

---

## File Structure

```
php/
├── index.php      # Login page (public)
├── setup.php      # Database setup (public)
├── dashboard.php  # Main dashboard (protected)
├── available.php  # Available books (protected)
├── add_book.php   # Add new book (protected)
├── edit_book.php  # Edit book (protected)
├── borrow.php     # Borrow book (protected)
├── return.php     # Return book (protected)
├── delete.php     # Delete book (protected)
├── logout.php     # Logout (protected)
├── auth.php       # Auth functions (helper)
└── db.php         # Database connection (helper)
```