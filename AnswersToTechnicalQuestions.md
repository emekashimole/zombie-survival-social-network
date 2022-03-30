### 1. How long did you spend on the coding test?

Estimated 8 hrs spread over the course of 7 days.

### 2. What would you add to your solution if you had more time?

**I would look into the following feature areas:**
- Notification Services
- Queuing
- Chating
- Caching
- Geo-spatial Analysis using Coordinates
- More Inituitive Frontend

### 3. How would you track down a performance issue in production? Have you ever had to do this?
Performance issues from my experience can typically be narrowed down to 3 main areas; Frontend, Backend or the Application Architecture itself. A first point of call would be to add test scripts on Postman to get a sense of the computation time and latency of the particular request that is having performance issues. Cloud tools such as AWS X-Ray, CloudWatch etc also help troubleshoot issues that have to do with performance.

### 4. How would you improve the APIs that you just created?
- Add more unit test scripts
- Ability to trade more than one item at a time
- Track item trades
- Better validation messages

### 5. Best practices
- Request validation
- Service-level dependency injection
- Custom exceptions
- Logging
- Use of database transaction blocks wherever required
- Consistent API responses with HTTP codes
- Custom API resources for formatting API responses before sending to frontend