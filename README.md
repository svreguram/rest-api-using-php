# Reguram POS API

**Author**: [Reguram](http://reguram.in)  
**Email**: [reguram@gmail.com](mailto:reguram@gmail.com)  
**Website**: [reguram.in](http://reguram.in)

## Overview

This repository contains the code for the Reguram POS API, a RESTful API built in PHP for accessing data from the POS system. The API is designed to allow customers to retrieve data for analytical purposes, providing read-only access to various tables such as transactions, products, and more.

## Features

- **RESTful API**: Built with PHP to handle data retrieval.
- **Read-Only Access**: Ensures that customers can only retrieve data without making any modifications.
- **API Key Authentication**: Secures access to the API using API keys.
- **Pagination**: Supports limiting and offsetting records to manage large datasets.
- **Date Range Filtering**: Allows filtering records based on a date range.
- **Dynamic Table Access**: Enables retrieving data from any specified table.

## API Endpoints

### 1. Get Transactions
- **URL**: `/transactions/read.php`
- **Method**: `GET`
- **Headers**: 
  - `x-api-key`: Your API key
- **Query Parameters**:
  - `table`: Name of the table (e.g., `transactions`)
  - `limit`: Number of records to retrieve (default: 100)
  - `offset`: Offset for pagination (default: 0)
  - `start_date`: Start date for filtering (optional)
  - `end_date`: End date for filtering (optional)
- **Response**:
  - `total_count`: Total number of matching records
  - `records`: Array of records

### 2. Get Total Record Count
- **URL**: `/transactions/count.php`
- **Method**: `GET`
- **Headers**: 
  - `x-api-key`: Your API key
- **Query Parameters**:
  - `table`: Name of the table (e.g., `transactions`)
  - `start_date`: Start date for filtering (optional)
  - `end_date`: End date for filtering (optional)
- **Response**:
  - `count`: Total number of records

### 3. Get Table Column Data Types
- **URL**: `/table/columns.php`
- **Method**: `GET`
- **Headers**: 
  - `x-api-key`: Your API key
- **Query Parameters**:
  - `table`: Name of the table
- **Response**:
  - Array of column names and their data types

### 4. Get List of Tables
- **URL**: `/tables/list.php`
- **Method**: `GET`
- **Headers**: 
  - `x-api-key`: Your API key
- **Response**:
  - Array of table names

## Installation

### Prerequisites
- PHP 7.x or higher
- MySQL database
- Apache web server
- Composer (optional, for dependency management)

### Setup
1. Clone the repository:
   ```bash
   git clone https://github.com/reguram/pos-api.git
   cd pos-api
   ```

2. Configure the database connection in `config/dbcon.php`.

3. Protect sensitive files using `.htaccess`:
   ```apache
   <Files "dbcon.php">
       Order Allow,Deny
       Deny from all
   </Files>
   ```

4. Set up the necessary API users in the `api_users` table with hashed API keys.

## Usage

Use tools like [Postman](https://www.postman.com/) to interact with the API. Ensure that you pass the correct API key in the `x-api-key` header.

### Example Request

```bash
GET /transactions/read.php?table=transactions&limit=100&offset=0&start_date=2023-01-01&end_date=2023-12-31
```

### Example Response

```json
{
    "total_count": 150,
    "records": [
        {
            "id": 1,
            "transaction_id": "TX123456",
            "created_at": "2023-01-01 12:00:00",
            ...
        }
    ]
}
```

## Contributing

If you wish to contribute to this project, feel free to fork the repository and submit a pull request.

## License

This project is open-source and available under the [MIT License](LICENSE).
