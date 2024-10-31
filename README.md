# Real-time Stock Price Aggregator

This application is a Laravel-based system that tracks real-time stock prices and stores them in a database. It fetches stock data from the Alpha Vantage API, updates the database with the latest prices, calculates the percentage change, and provides a reporting interface to view the stock price history.

## Features

- Real-time stock price fetching from Alpha Vantage API.
- Stores current and previous stock prices, with percentage changes.
- Uses caching to reduce API calls and improve efficiency.
- Displays real-time stock price reports using Livewire.
- Handles rate limits and API errors gracefully.
- REST API endpoint to fetch latest stock prices.

## Prerequisites

- PHP >= 8.1
- Composer
- Laravel >= 9.x
- Alpha Vantage API Key
- MySQL or any other compatible database

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/george2ps/Real-time-Stock-Price-Aggregator.git
   cd your project directory
   ```

2. Install dependencies using Composer:

   ```bash
   composer install
   ```

3. Copy the `.env.example` file to `.env`:

   ```bash
   cp .env.example .env
   ```

4. Generate the application key:

   ```bash
   php artisan key:generate
   ```

5. Configure the database connection in the `.env` file:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_username
   DB_PASSWORD=your_database_password
   ```

6. Set up the Alpha Vantage API key in the `.env` file:

   ```env
   ALPHA_VANTAGE_KEY=your_api_key_here
   ```

7. Run the migrations and seeders to set up the database:

   ```bash
   php artisan migrate --seed
   ```


8. Serve the application:

   ```bash
   php artisan serve
   ```

## Usage

### Fetch Stock Prices

The application includes a scheduled command to fetch real-time stock prices and update the database.

- **Command:**

  ```bash
  php artisan app:fetch-stock-prices
  ```

- **Scheduling:**

  Add the following cron job to your server to run the command every minute:

  ```
  * * * * * php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1
  ```

### View Stock Reports

The application includes a Livewire component to view real-time stock reports. You can access it by navigating to the application's URL.

### REST API Endpoint

The application also includes an API route to fetch stock prices as a REST API.

- **Route:**

  ```php
  Route::get('/{function}', [\App\Http\Controllers\StockPricesController::class, 'query'])->name('api.query');
  ```

- **Example Request:**

  ```php
  https://your-root-url/api/getLatestStockPrices
  ```

  This route directs to the `query` function in the `StockPricesController`, which handles different functions based on the input.

- **Controller Function:**

  ```php
  public function query($function)
  {
      switch ($function){
          case 'getLatestStockPrices': return $this->getLatestStockPrices();
          default: return $this->getLatestStockPrices();
      }
  }
  ```

  The `query` function uses the `getLatestStockPrices` trait to fetch stock data.

- **Trait Function (`getLatestStockPrices`)**:

  ```php
  public function getLatestStockPrices()
  {
      $stocks = config('services.alphavantage.stocks');

      // Get Stock data from cache if available
      $getStockData = Cache::get('latest-stock-prices');

      if($getStockData == null) {
          try {
              $getStockData = StockPrice::whereIn('symbol', array_keys($stocks))->get()->toArray();

              // Store data in Cache for 1 minute
              Cache::put('latest-stock-prices', $getStockData, 60);
          }catch (\Exception $e){
              Log::error('Failed to fetch stock data from Database', [ 'message' => $e->getMessage() ]);

              return response()->json([
                  'status' => 500,
                  'message' => $e->getMessage(),
                  'data' => []
              ]);
          }

      }

      return response()->json([
          'status' => 200,
          'data' => $getStockData
      ]);
  }
  ```

  This function fetches the stock data from the cache if available, or from the database if not cached, and returns it as a JSON response.

## Testing

To run the tests, use the following command:

```bash
php artisan test
```

The tests include:

- Fetching stock prices from cache and database.

## Project Structure

- **`app/Console/Commands/FetchStockPrices.php`**: Command to fetch real-time stock prices.
- **`app/Models/StockPrice.php`**: Model representing the stock prices table.
- **`app/Http/Livewire/StockPricesReport.php`**: Livewire component for displaying stock reports.
- **`database/seeders/StockPricesSeeder.php`**: Seeder to populate initial stock data.
- **`app/Http/Controllers/StockPricesController.php`**: Controller to handle API requests for stock prices.

## Technologies Used

- **Laravel**: PHP framework for building the application.
- **Livewire**: Used for real-time UI updates.
- **Bootstrap 5**: Used for UI styling.
- **Fontawesome 6**: Used for UI styling.
- **Alpha Vantage API**: Provides real-time stock price data.

## Error Handling

The application includes error handling for:

- **API Rate Limits**: Logs the error and pauses before retrying.
- **Connection Issues**: Logs exceptions to help with debugging.
- **Unexpected Response Structure**: Logs warnings when the response does not match the expected format.
