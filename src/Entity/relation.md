### Has One

1 к 1, просто декомпозиция

    #[HasOne(target: Address::class)]

Опции:
- load:	lazy (default) / eager // как загружать
- cascade: true // обновлять при сохранении родителя
- nullable: false // возможны ли "сироты"
- fkAction: CASCADE (default) / NO ACTION / SET NULL (onDelete / onUpdate)
- fkOnDelete: CASCADE (default) / NO ACTION / SET NULL (onDelete, но с большим приоритетом)

### Has Many

Много дочерних компонентов (массив или коллекция)

    #[HasMany(target: Post::class)]

Опции:
- те же
- collection: (Cycle\ORM\Collection\ArrayCollectionFactory)

### Belong To

Обратная зависимость (ссылка на родителя)

    #[BelongsTo(target: User::class)]

### Refers To

Ссылки на один и тот же объект (циклическая, на себя)

    #[RefersTo(target: Comment::class)]

### Many To Many

Использует промежуточную сущность

     #[ManyToMany(target: Tag::class, through: UserTag::class)]

- те же
- collection: (Cycle\ORM\Collection\ArrayCollectionFactory)

### Embedding

Для непереиспользуемых сущностей

    #[Embeddable(columnPrefix: 'credentials_')]
    <->
    #[Embedded(target: 'UserCredentials')]

Опции:
- load:	lazy (default) / eager // как загружать

### Morphed

Не рекомендуется

### Collections

    ArrayCollectionFactory
    DoctrineCollectionFactory
    IlluminateCollectionFactory
    LoophpCollectionFactory
