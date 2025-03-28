### Overview
The **ActionQueue** class is designed as a backend service to manage user interactions and trigger sequential actions. Instead of executing a single operation in isolation, it allows different workflows to be combined dynamically.

For example, in a banking application where a document needs to be approved by a customer:
1. A **notification (NotificationAction)** is sent to inform the user.
2. If the user opens the app, a **dialog (DialogAction)** appears with options:
   - "I want to read the document" → The user is navigated to the document page via **NavigationAction**.
   - "Sign it directly" → The system executes an **XHRAction** (to be implemented in future versions) to complete the process.

This system enables actions to form a chain rather than being executed as isolated events. The **ActionQueue** class streamlines this workflow, ensuring flexibility and scalability for various business logic scenarios.

### Supported Scenarios
#### 1. Handling Incoming Messages
When a new message arrives, the backend can trigger a **NotificationAction** to alert the user. If the user clicks on the notification, a **NavigationAction** redirects them to the messaging screen automatically.

#### 2. Multi-Step User Confirmation
In cases where the user needs to approve or reject an action, a **DialogAction** can be used. The dialog presents options, each of which may trigger further actions:
- Accepting an agreement may lead to a **NavigationAction** for reviewing details.
- Rejecting may trigger a **ToastAction** confirming the decision.

#### 3. Dynamic UI Responses
The system can dynamically update the UI based on user interactions. For instance:
- A **ToastAction** can be used to display real-time success or error messages.
- A **LinkAction** allows users to be redirected to external resources.

These scenarios highlight how **ActionQueue** enables seamless, structured user interaction flows in applications.



### Features
- Support Standard Markdown / CommonMark and GFM(GitHub Flavored Markdown);

# Action Mapper

![](https://pandao.github.io/editor.md/images/logos/editormd-logo-180x180.png)

**Table of Contents**

[TOCM]

[TOC]


# Usage Example
## Just Toast

```php
$actionQueue = new ActionQueue();
$actionQueue
   ->add(new ToastAction(
				type: "SUCCESS",
				title: "Title",
				textBody: "text Body"
			))
```
## Toast > Link

```php
$actionQueue = new ActionQueue();
$actionQueue
   ->add(new ToastAction(
				type: "SUCCESS",
				title: "Title",
				textBody: "text Body"
			))
	->add(new LinkAction(
				url: "https://google.com"
			))
```

## Notification > Toast > Dialog (one option)
> dialog option 1: toast > navigation

```php
$actionQueue = new ActionQueue();
$actionQueue
  ->add(new NotificationAction(
				title: "Messages",
				body: "Messages.Show"
			))
		->add(new ToastAction(
				type: "SUCCESS",
				title: "Title",
				textBody: "text Body"
			))
		->add(new DialogAction(
				title: "SUCCESS",
				textBody: "Title",
				options: [
					new DialogActionOption(
						text: "Go to message",
						actionQueue: (new ActionQueue())
							->add(new ToastAction(
								type: "SUCCESS",
								title: "Go to message",
								textBody: "text Body"
							))
							->add(new NavigationAction(
									name: "Messages",
									screen: "Messages.Show",
									params: [
										"conversation" => $conversation
									]
								))
					)
				]
			))
```

## Dialog (multiple options)
Let's say you want to send a notification and you want to give the customer two options, option 1; view the notification, option 2; mark as read
> option 1: toast
> option 2: link

```php
$actionQueue = new ActionQueue();
$actionQueue
		->add(new DialogAction(
				title: "You have new message!",
				textBody: "What you want to?",
				options: [
					new DialogActionOption(
						text: "Add signatured",
						actionQueue: (new ActionQueue())
							->add(new ToastAction(
								type: "SUCCESS",
								title: "Thanks",
								textBody: ".."
							))
					),
				new DialogActionOption(
					text: "Read first",
					actionQueue: (new ActionQueue())
						->add(new LinkAction(
							url: "https:://mywebsite.com/notification/1",
						))
				)
				]
			))
```

# Class: ActionQueue
## Properties
- **`private array $queue`**: Holds the queue of actions.

## Methods
- **`public function init(): self`**
  - Initializes the queue and returns the current instance.

- **`public function add(BaseAction $action): self`**
  - Adds a new action to the queue, ensuring it is placed in the deepest available position.
  - **Parameters:**
    - `$action` (BaseAction) - The action to be added.
  - **Returns:** `self`

- **`private function insertDeepest(array &$queue, array $newAction): void`**
  - Recursively finds the deepest position in the queue and inserts the new action.
  - **Parameters:**
    - `$queue` (array) - The current queue structure.
    - `$newAction` (array) - The new action data.

- **`public function queue(): array`**
  - Retrieves the current queue of actions.
  - **Returns:** `array`

- **`public function get(): array`**
  - Converts the queue into a JSON-compatible format.
  - **Returns:** `array`

# Abstract Class: BaseAction
## Methods
- **`abstract protected function type(): string`**
  - Must be implemented in derived classes to specify the action type.

- **`public function get(): array`**
  - Returns the action type and associated data.
  - **Returns:** `array`

# Action Implementations
## Class: NavigationAction
- **Constructor Parameters:**
  - `$name` (string) - Name of the navigation target.
  - `$screen` (string) - Target screen identifier.
  - `$params` (array) - Additional parameters.

- **`protected function type(): string`**
  - Returns: `"navigation"`

## Class: ToastAction
- **Constructor Parameters:**
  - `$type` (string) - Type of toast message (e.g., success, error).
  - `$title` (string) - Title of the message.
  - `$textBody` (string) - Main content.

- **`protected function type(): string`**
  - Returns: `"toast"`

## Class: DialogAction
- **Constructor Parameters:**
  - `$title` (string) - Dialog title.
  - `$textBody` (string) - Main content.
  - `$options` (array) - List of `DialogActionOption` instances.

- **`protected function type(): string`**
  - Returns: `"dialog"`

### Class: DialogActionOption
- **Constructor Parameters:**
  - `$text` (string) - Button text.
  - `$actionQueue` (ActionQueue) - Associated action queue.

## Class: LinkAction
- **Constructor Parameters:**
  - `$url` (string) - Target URL.

- **`protected function type(): string`**
  - Returns: `"link"`

## Class: NotificationAction
- **Constructor Parameters:**
  - `$title` (string) - Notification title.
  - `$body` (string) - Notification content.

- **`protected function type(): string`**
  - Returns: `"notification"`
