# Request Object validator 
A simple library to force and make consistent development REST API controllers.
It allows to define structure of the payload and it validator constraints. It also provides much easier way to access validated data from within the controller body. 

## Installation
`composer require pln0w/rest-api-validator`

Add following section to your _services.yml_

```yaml
Pawly\RestApiValidator\Resolver\CustomRequestResolver:
    arguments:
      - '@validator'
    tags:
      - { name: controller.request_value_resolver }
```

Each request object must have _request_stack_ service injected, i.e:
```yaml
User\Infrastructure\Request\RegisterUserRequest:
        arguments:
            - '@request_stack'
```

## Usage
* Steps to provide validation to your controllers:
  
    * Create custom request overriding _Pawly\RestApiValidator\Request\AbstractCustomRequest_
    * Define request class properties that you want to map request values with
    * Add validation constraints to _$metadata_ (no yaml, no addnotations - pure PHP config)
    * _(optional)_ Override getters for properties in needed or add constraint explicit against getter  
    _See validation docs:_ https://symfony.com/doc/master/validation.html  
    
    #### Example 1.
    ```php
    <?php
    declare(strict_types=1);
    
    namespace User\Infrastructure\Request;
    
    use Pawly\RestApiValidator\Request\AbstractCustomRequest;
    use Symfony\Component\Validator\Constraints as Assert;
    
    class RegisterUserRequest extends AbstractCustomRequest
    {
        protected ?string $email = null;
        protected ?string $password = null;
    
        /**
         * @inheritDoc
         */
        public function getValidationRules()
        {
            return new Assert\Collection([
                'email' => new Assert\Email(),
                'password' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 6])
                ]
            ]);
        }
    }
    ```
    * Create controller having this request injected

    ```php
    <?php
    declare(strict_types=1);
    
    namespace User\Infrastructure\Controller;
    
    use Pawly\RestApiValidator\Response\ApiResponse;
    use User\Infrastructure\Request\RegisterUserRequest;
    
    final class RegisterUserAction
    {
        public function __invoke(RegisterUserRequest $request): ApiResponse
        {
            // do some stuff ...
            $email = $request->getEmail();
            
            return ApiResponse::json(['email' => $email], ApiResponse::HTTP_OK);
        }
    }
    ```  
          
    #### Different custom request validation example
    ```php
    <?php
    declare(strict_types=1);
    
    namespace User\Infrastructure\Request;
    
    use Pawly\RestApiValidator\Request\AbstractCustomRequest;
    use Symfony\Component\Validator\Constraints as Assert;
    use Shared\Domain\ValueObject\Email;

    class RegisterUserRequest extends AbstractCustomRequest
    {
        protected ?string $email = null;
        protected ?string $password = null;
    
        /**
         * @inheritDoc
         */
        public function getValidationRules()
        {
            return new Assert\Collection([
                'email' => new Assert\Email(),
                'password' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 6])
                ]
            ]);
        }
      
        public function getEmail(): Email
        {
             return new Email($this->email);
        }
    }
    ```
* Validation errors handling  

If request has invalid data, then exception is thrown and handled in subscriber, that will provide following message structure:  
```json
{
    "status": 422,
    "message": "Request validation error",
    "details": {
        "email": "This value should not be correct email address.",
        "password": "This value should not be blank."
    }
}
```
## More
#### Validate responses interface
If you want to ensure you use unified response for controllers, you can enable checking response interface by addint below definition to your _services.yaml_
```yaml
Pawly\RestApiValidator\Subscriber\ValidateResponseInterfaceSubscriber:
    tags:
      - { name: kernel.event_subscriber, event: kernel.response }
```