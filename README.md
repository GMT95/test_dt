## My Thoughts

### Overall Code:

- The code is using repository pattern, but it does not strictly follow the repository pattern priciples. A repository should only be an  abstraction to Model class. But in this code sample business logic is written inside the repository, the code leans more towards Service pattern.
- The code is not terrible code, it is just ok code. It may just be working but it is difficult to maintain, many if conditions are repeated, they can be extracted in a single method or in model class. It can be made better by using Laravel's feature instead of relying on php functions. One such instance is using multiple if conditions for conditional queries, where `ifs` can be replaced with Laravel's `when()` Query builder method. Request validation is also not present which makes code some what insecure also. 

### Booking Controller

- No error handling, Methods don't have a try/catch block, it was only present in one method `resendSMSNotifications`, I have wrapped methods inside try/catch block.
- There was no default response in `index` method
- Request Validation is not done.
- Multiple if else statements are present in `distanceFeed` method, which are replaced with ternary statements.
- `null` response in `getHistory` method, when the condition is not met. Controller function should not return null but some form of response (like error message etc.).
- Variables are declared inside if statement, so when if block does not run the vairables are not set, which results in error
- Function responses are assigned to variables, these variables are not used anywhere like the `$job_data` variable in `resendSMSNotifications` method.

### Booking Repository

- In `getUsersJobs()` method, use select instead of pluck, to select from DB. Use jobs + fields for `getTranslatorJobs`method.
- In `store()` method, many conditions were there which used `in_array` php method, replace it with a single loop and checking conditions inside it.
- In all places where `findOrFail` method is used, a try/catch block must be prsent, adding one in `storeJobEmail` method.
- Not good practice to use `@` symbol to suppress errors, instead use null coelscing or try/catch block.
- Simply return statement in `isNeedToSendPush` method.
- Replace FirePHPHandler and StreamHandler with Laravel built in Logger facade.
- PHP CURL can be replaced with Laravel's HTTP client or Guzzle client for better readability.
- Some Conditions are repeated many times we can use some constants array or laravel model attributes
- Replacing conditional `where()` queries with Laravel `when()` Query builder method in `getAll` & `alerts` method

### Code to Write Test

- Test written for **App/Helpers/TeHelper.php method willExpireAt** (*location: my_tests/willExpireAtTest.php*).
  *Note: made some minor changes to willExpireAt method, to cover all conditions* 