# Laravel Blog API - Learning Spatie Laravel Data

A RESTful Blog API built with Laravel 12 to learn and demonstrate **[Spatie Laravel Data v4](https://spatie.be/docs/laravel-data)** best practices.

This project serves as a practical learning resource for implementing DTOs (Data Transfer Objects) with Laravel Data, showcasing features like validation, transformations, lazy loading, computed properties, and collections.

## Features

- **User Authentication** with Laravel Sanctum
- **CRUD Operations** for Posts, Categories, and Comments
- **Relationship Management** (Users, Posts, Categories, Comments)
- **Repository Pattern** for data access
- **Service Layer** for business logic
- **Complete Laravel Data v4 Implementation**
  - DTOs for all API resources
  - Lazy loading for relationships
  - Computed properties
  - Built-in validation
  - Custom validation messages
  - Proper collection handling (DataCollection, PaginatedDataCollection)
  - Type casting (dates, relationships)

## Tech Stack

- **Laravel** 12.x
- **PHP** 8.2+
- **Spatie Laravel Data** 4.18
- **Laravel Sanctum** for API authentication
- **MySQL** (or your preferred database)

## Installation

```bash
# Clone the repository
git clone <repository-url>
cd blog-api

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure your database in .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=blog_api
# DB_USERNAME=root
# DB_PASSWORD=

# Run migrations
php artisan migrate

# (Optional) Seed the database
php artisan db:seed

# Start the development server
php artisan serve
```

The API will be available at `http://localhost:8000/api/v1`

## API Endpoints

### Authentication

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/v1/register` | Register a new user | No |
| POST | `/v1/login` | Login user | No |
| POST | `/v1/logout` | Logout user | Yes |
| GET | `/v1/user` | Get authenticated user | Yes |

### Posts

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/v1/posts` | Get all posts (paginated) | No |
| GET | `/v1/posts/{idOrSlug}` | Get single post by ID or slug | No |
| GET | `/v1/users/{userId}/posts` | Get posts by user | No |
| GET | `/v1/categories/{categoryId}/posts` | Get posts by category | No |
| POST | `/v1/posts` | Create a new post | Yes |
| PUT | `/v1/posts/{id}` | Update a post | Yes |
| DELETE | `/v1/posts/{id}` | Delete a post | Yes |

### Categories

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/v1/categories` | Get all categories | No |
| GET | `/v1/categories/{id}` | Get single category | No |
| POST | `/v1/categories` | Create a new category | Yes |
| DELETE | `/v1/categories/{id}` | Delete a category | Yes |

### Comments

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/v1/posts/{postId}/comments` | Get comments for a post | No |
| POST | `/v1/comments` | Create a new comment | Yes |
| DELETE | `/v1/comments/{id}` | Delete a comment (own only) | Yes |

## Laravel Data Implementation Examples

### 1. Basic Data Object with Validation

```php
// app/Data/Post/CreatePostData.php
class CreatePostData extends Data
{
    public function __construct(
        #[Required, Min(3)]
        public string $title,

        #[Required, Min(10)]
        public string $content,

        #[Required]
        public string $excerpt,

        #[ArrayType, Exists('categories', 'id')]
        public array $categoryIds = [],
    ) {}

    public static function messages(): array
    {
        return [
            'title.required' => 'O título é obrigatório',
            'title.min' => 'O título deve ter no mínimo 3 caracteres',
        ];
    }
}
```

**Usage in Controller:**
```php
public function store(CreatePostData $data)
{
    // Data is automatically validated and type-cast
    $post = $this->postService->create($data, auth()->id());
    return PostData::from($post);
}
```

### 2. Optional Fields for Updates

```php
// app/Data/Post/UpdatePostData.php
class UpdatePostData extends Data
{
    public function __construct(
        #[Min(3)]
        public string|Optional $title,

        #[Min(10)]
        public string|Optional $content,

        public string|Optional $excerpt,

        #[ArrayType, Exists('categories', 'id')]
        public array|Optional $categoryIds,
    ) {}
}
```

**Usage in Service:**
```php
if (!$data->title instanceof \Spatie\LaravelData\Optional) {
    $updateData['title'] = $data->title;
}
```

### 3. Lazy Loading Relationships

```php
// app/Data/Post/PostData.php
class PostData extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public string $content,

        // Lazy load author - included only when requested
        public Lazy|UserData $author,

        /** @var Lazy|DataCollection<CommentData> */
        public Lazy|DataCollection|null $comments = null,

        /** @var Lazy|DataCollection<CategoryData> */
        public Lazy|DataCollection|null $categories = null,
    ) {}
}
```

**Usage:**
```php
// Without relationships
return PostData::from($post);

// With specific relationships
return PostData::from($post)->include('author', 'comments', 'categories');
```

### 4. Computed Properties

```php
// In PostData
#[Computed]
public int $commentsCount = 0;
```

**Model Accessor:**
```php
// In Post Model
protected function commentsCount(): Attribute
{
    return Attribute::make(
        get: fn() => $this->comments()->count(),
    );
}
```

### 5. Date Casting and Field Mapping

```php
#[MapName('published_at')]
#[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i:s')]
public ?Carbon $publishedAt,

#[MapName('created_at')]
#[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i:s')]
public Carbon $createdAt,
```

### 6. Collection Handling (v4 Syntax)

```php
// For paginated results
public function getByUser(int $userId, int $perPage = 15): PaginatedDataCollection
{
    $posts = $this->postRepository->getByUser($userId, $perPage, ['author']);

    // Explicitly specify collection type
    return PostData::collect($posts, PaginatedDataCollection::class);
}

// For regular collections
public function getAll(): DataCollection
{
    $categories = $this->categoryRepository->all();

    return CategoryData::collect($categories, DataCollection::class);
}
```

### 7. Including Lazy Data in Collections

```php
public function getByPost(int $postId, int $perPage = 15): PaginatedDataCollection
{
    $comments = $this->commentRepository->getByPost($postId, $perPage);

    return CommentData::collect($comments, PaginatedDataCollection::class)
        ->include('author'); // Include lazy-loaded author
}
```

## Project Structure

```
app/
├── Data/                          # Laravel Data DTOs
│   ├── Category/
│   │   ├── CategoryData.php       # Resource DTO
│   │   └── CreateCategoryData.php # Creation DTO
│   ├── Comment/
│   │   ├── CommentData.php
│   │   └── CreateCommentData.php
│   ├── Post/
│   │   ├── PostData.php
│   │   ├── CreatePostData.php
│   │   └── UpdatePostData.php
│   └── User/
│       ├── UserData.php
│       ├── CreateUserData.php
│       └── UpdateUserData.php
├── Http/
│   └── Controllers/
│       └── Api/V1/                # API Controllers
├── Models/                        # Eloquent Models
├── Repositories/                  # Data Access Layer
└── Services/                      # Business Logic Layer
```

## Key Learnings

### Laravel Data v3 → v4 Migration

**Changed:**
- ❌ `collection()` method removed
- ✅ Use `collect($data, CollectionType::class)` instead

**Example:**
```php
// v3 (deprecated)
return PostData::collection($posts);

// v4 (correct)
return PostData::collect($posts, PaginatedDataCollection::class);
```

### Best Practices Implemented

1. **Always eager load required relationships** to avoid N+1 queries
   ```php
   $posts = $this->postRepository->getByUser($userId, $perPage, ['author']);
   ```

2. **Use `#[Computed]` for derived properties** instead of Lazy
   ```php
   #[Computed]
   public int $commentsCount = 0;
   ```

3. **Specify collection types explicitly** for proper type hinting
   ```php
   PostData::collect($posts, PaginatedDataCollection::class);
   ```

4. **Separate DTOs by purpose**: CreateData, UpdateData, ResourceData

5. **Use Optional for partial updates** to distinguish between null and absent fields

## Example API Requests

### Register User
```bash
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Create Post
```bash
curl -X POST http://localhost:8000/api/v1/posts \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "title": "My First Post",
    "content": "This is the content of my first post.",
    "excerpt": "A brief summary",
    "categoryIds": [1, 2]
  }'
```

### Get Post with Relationships
```bash
curl http://localhost:8000/api/v1/posts/my-first-post
```

## Resources

- [Spatie Laravel Data Documentation](https://spatie.be/docs/laravel-data)
- [Laravel 12 Documentation](https://laravel.com/docs/12.x)
- [Laravel Sanctum Documentation](https://laravel.com/docs/12.x/sanctum)

## License

This project is open-sourced for learning purposes under the MIT license.
