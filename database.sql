DROP TABLE IF EXISTS urls;
DROP TABLE IF EXISTS checks;

CREATE TABLE urls (
    id bigint PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    name varchar(255) UNIQUE NOT NULL,
    created_at timestamp
);

CREATE TABLE checks (
    id bigint PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    url_id bigint NOT NULL,
    status_code bigint,
    h1 text,
    title text,
    description text,
    created_at timestamp
);
