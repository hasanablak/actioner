### Overview
The **ActionQueue** class is designed to manage a queue of actions, which can be executed in a hierarchical manner. It supports various action types, such as navigation, toast messages, dialogs, and links.

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
