# Metrics

A POF to proves using mysql as a time series database, this project depends heavily on MySQL query cache to works as it bests performance.

## Project Setup

```bash
$ composer install
```

## Usage

First run the collector server:

```bash
$ bin/server
```

To see a simple dashboard of server metrics at http://localhost:5000/dashboard.html:
```bash
$ bin/web
```

See the function `metric_collect` on `library/api.php` to learn how to send a metric to collector.

## License

Copyright (C) 2014 Rodrigo Moyle <rodrigorm@gmail.com>

This program is free software: you can redistribute it and/or modify
it under the terms of the Lesser GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the Lesser GNU General Public License
along with this program. If not, see http://www.gnu.org/licenses/.
