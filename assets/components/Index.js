import React, { useEffect, useState } from "react";
import { Button, Rating, Spinner } from "flowbite-react";

const Index = (props) => {
  const [movies, setMovies] = useState([]);
  const [filter, setFilter] = useState(0);
  const [filterCategory, setFilterCategory] = useState("");
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);

  const fetchMovies = () => {
    setLoading(true);
    const options = {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ filter: filter, category: filterCategory }),
    };
    return fetch("/api/movies", options)
      .then((response) => response.json())
      .then((data) => {
        setMovies(data.movies);
        setLoading(false);
      });
  };
  const fetchCategories = () => {
    setLoading(true);

    return fetch("/api/categories")
      .then((response) => response.json())
      .then((data) => {
        console.log(data);
        setCategories(data.categories);
        setLoading(false);
      });
  };

  useEffect(() => {
    fetchCategories();
    fetchMovies();
  }, []);
  useEffect(() => {
    fetchMovies();
  }, [filter]);
  useEffect(() => {
    fetchMovies();
  }, [filterCategory]);

  return (
    <Layout>
      <style>
        {`
          .selectInput {
           height: 4rem;
           border: 1px solid lightgray;
           border-radius: 10px;
           padding-left: 20px;
           font-size: 1.2em;
           font-weight: 300;
           padding-right: 20px;
           width: 100%;
           outline: none;
           box-shadow: none;
          }
          .selectInput:focus {
            outline: none;
          border: 3px solid lightblue;
          }
          .orderBy{
            font-size: 1.4em;
            margin-bottom: 10px;
            font-weight: 300;
          }
        `}
      </style>
      <Heading />
      <div className="flex flex-col md:flex-row gap-4">
        <div className="w-full md:w-1/2">
          <Filters
            isCategory={false}
            filters={[
              { value: 1, title: "Più recenti" },
              { value: 2, title: "Rating" },
            ]}
            setFilter={setFilter}
          />
        </div>
        <div className="w-full md:w-1/2">
          <Filters
            filters={categories}
            isCategory={true}
            setFilter={setFilterCategory}
          />
        </div>
      </div>
      <MovieList loading={loading}>
        {movies.map((item, key) => (
          <MovieItem key={key} {...item} />
        ))}
      </MovieList>
    </Layout>
  );
};

const Filters = (props) => {
  return (
    <section className="bg-white dark:bg-gray-900">
      <div className="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
        <select
          className="selectInput"
          onChange={(e) => {
            props.setFilter(e.target.value);
          }}
        >
          <option value={0}>
            {props.isCategory
              ? "Select a genre"
              : "Select an order"}
          </option>
          {props.filters.map((filter) => {
            return (
              <option value={filter.value}>
                {props.isCategory ? filter.value : filter.title}
              </option>
            );
          })}
        </select>
      </div>
    </section>
  );
};

const Layout = (props) => {
  return (
    <section className="bg-white dark:bg-gray-900">
      <div className="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
        {props.children}
      </div>
    </section>
  );
};

const Heading = (props) => {
  return (
    <div className="mx-auto max-w-screen-sm text-center mb-8 lg:mb-16">
      <h1 className="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">
        Movie Collection
      </h1>

      <p className="font-light text-gray-500 lg:mb-16 sm:text-xl dark:text-gray-400">
        Explore the whole collection of movies
      </p>
    </div>
  );
};

const MovieList = (props) => {
  if (props.loading) {
    return (
      <div className="text-center">
        <Spinner size="xl" />
      </div>
    );
  }

  return (
    <div className="grid gap-4 md:gap-y-8 xl:grid-cols-6 lg:grid-cols-4 md:grid-cols-3">
      {props.children}
    </div>
  );
};

const MovieItem = (props) => {
  return (
    <div className="flex flex-col w-full h-full rounded-lg shadow-md lg:max-w-sm">
      <div className="grow">
        <img
          className="object-cover w-full h-60 md:h-80"
          src={props.image}
          alt={props.title}
          loading="lazy"
        />
      </div>

      <div className="grow flex flex-col h-full p-3">
        <div className="grow mb-3 last:mb-0">
          {props.year || props.rating ? (
            <div className="flex justify-between align-middle text-gray-900 text-xs font-medium mb-2">
              <span>{props.year}</span>

              {props.rating ? (
                <Rating>
                  <Rating.Star />

                  <span className="ml-0.5">{props.rating}</span>
                </Rating>
              ) : null}
            </div>
          ) : null}

          <h3 className="text-gray-900 text-lg leading-tight font-semibold mb-1">
            {props.title}
          </h3>

          <p className="text-gray-600 text-sm leading-normal mb-4 last:mb-0">
            {props.plot.substr(0, 80)}...
          </p>
        </div>

        {props.wikipedia_url ? (
          <Button
            color="light"
            size="xs"
            className="w-full"
            onClick={() => window.open(props.wikipedia_url, "_blank")}
          >
            More
          </Button>
        ) : null}
      </div>
    </div>
  );
};

export default Index;
