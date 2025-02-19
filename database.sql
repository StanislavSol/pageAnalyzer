DROP TABLE IF EXISTS urls;
DROP TABLE IF EXISTS url_checks;

CREATE TABLE urls (
    id bigint PRIMARY KEY,
    name varchar(255),
    created_at DATE
);

CREATE TABLE url_checks (
    id bigint PRIMARY KEY,
    url_id bigint,
    status_code bigint NOT NULL,
    h1 text,
    title text,
    description text,
    created_at DATE DEFAULT CURRENT_TIMESTAMP
);
