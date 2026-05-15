# NextDeveloper Generator

A Laravel code-generation library that scaffolds the full NextDeveloper module structure from a database schema. Point it at a database and it generates models, services, HTTP controllers, transformers, filters, events, event handlers, tests, and more — all following NextDeveloper conventions, ready to drop into a Laravel application.

## What It Generates

Given a target database and table list, the Generator produces:

| Artefact | Location |
|---|---|
| Eloquent models | `src/Database/Models/` |
| Service classes | `src/Services/` and `src/Services/AbstractServices/` |
| HTTP controllers | `src/Http/Controllers/` |
| Transformers | `src/Http/Transformers/` and `AbstractTransformers/` |
| Query filters | `src/Database/Filters/` |
| Events | `src/Events/` |
| Event handlers | `src/EventHandlers/` |
| Broadcasts | `src/Broadcasts/` |
| Authorization roles | `src/Authorization/Roles/` |
| Test stubs | `tests/` |
| Route files | `routes/` |

## Artisan Commands

```bash
# Generate the full module structure from the database
php artisan generate:structure

# Generate only the database layer (models, filters, observers)
php artisan generate:database
```

## How It Works

1. The Generator reads the target database schema — tables, columns, foreign keys, and constraints.
2. It maps each table to a module entity and resolves relationships from foreign key definitions.
3. Stub templates are rendered for each artefact type and written to the output path.
4. Generated abstract classes hold the scaffolded code; concrete classes above the `EDIT AFTER HERE` marker are preserved across re-generations.

## Architecture

The Generator is built around a set of service classes, each responsible for one layer:

- `Services/Database/` — model, filter, and observer generation
- `Services/Http/` — controller, transformer, and request generation
- `Services/Services/` — service class generation
- `Services/Events/` — event and event handler generation
- `Services/Structure/` — directory layout and provider scaffolding

## Commercial Support

Please let us know if you need any commercial support. We will be happy to help you on your project and/or applying this library in your project.

support@plusclouds.com

---

## Our Libraries

This library is part of the **NextDeveloper / PlusClouds open-source ecosystem**. Browse all available libraries and find the right building blocks for your next project:

[https://plusclouds.com/us/solutions/libraries](https://plusclouds.com/us/solutions/libraries)

---

## Join the Community

We believe great software is built together. The PlusClouds developer community is a place where engineers share ideas, ask questions, showcase what they have built, and help shape the direction of these libraries. Whether you are integrating a single package or building an entire platform on top of our stack, you are very welcome here.

Come and join us — we would love to see what you build:

[https://plusclouds.com/us/community](https://plusclouds.com/us/community)
