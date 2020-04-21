<?php
/**
 * Created by KiloFrameWork
 * User: xiejiawei<print_f@hotmail.com>
 * Date: 2020/4/3
 * Time: 9:49
 */

namespace kilophp;

use Illuminate\Database\Concerns\BuildsQueries;
use Illuminate\Database\Query\Builder;

/**
 * 基础模型类
 * Class Model
 * @method static Builder select($columns = ['*'])
 * @method static Builder distinct()
 * @method static Builder from($table, $as = null)
 * @method static Builder join($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
 * @method static Builder leftJoin($table, $first, $operator = null, $second = null)
 * @method static Builder leftJoinWhere($table, $first, $operator, $second)
 * @method static Builder leftJoinSub($query, $as, $first, $operator = null, $second = null)
 * @method static Builder rightJoin($table, $first, $operator = null, $second = null)
 * @method static Builder rightJoinWhere($table, $first, $operator, $second)
 * @method static Builder rightJoinSub($query, $as, $first, $operator = null, $second = null)
 * @method static Builder crossJoin($table, $first = null, $operator = null, $second = null)
 * @method static Builder where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static Builder orWhere($column, $operator = null, $value = null)
 * @method static Builder groupBy(...$groups)
 * @method static Builder groupByRaw($sql, array $bindings = [])
 * @method static Builder having($column, $operator = null, $value = null, $boolean = 'and')
 * @method static Builder havingBetween($column, array $values, $boolean = 'and', $not = false)
 * @method static Builder havingRaw($sql, array $bindings = [], $boolean = 'and')
 * @method static Builder orderBy($column, $direction = 'asc')
 * @method static Builder orderByDesc($column)
 * @method static Builder offset($value)
 * @method static Builder limit($value)
 * @method static Builder forPage($page, $perPage = 15)
 * @method static Builder forPageBeforeId($perPage = 15, $lastId = 0, $column = 'id')
 * @method static Builder forPageAfterId($perPage = 15, $lastId = 0, $column = 'id')
 * @method static Builder union($query, $all = false)
 * @method static Builder unionAll($query)
 * @method static Builder pluck($column, $key = null)
 * @method static Builder count($columns = '*')
 * @method static Builder min($column)
 * @method static Builder max($column)
 * @method static Builder sum($column)
 * @method static Builder avg($column)
 * @method static Builder insert(array $values)
 * @method static Builder insertGetId(array $values, $sequence = null)
 * @method static Builder update(array $values)
 * @method static Builder updateOrInsert(array $attributes, array $values = [])
 * @method static Builder increment($column, $amount = 1, array $extra = [])
 * @method static Builder decrement($column, $amount = 1, array $extra = [])
 * @method static Builder delete($id = null)
 * @method static Builder skip($value)
 * @method static Builder take($value)
 * @method static Builder reorder($column = null, $direction = 'asc')
 * @method static Builder lock($value = true)
 * @method static Builder lockForUpdate()
 * @method static Builder toSql()
 * @method static Builder find($id, $columns = ['*'])
 * @method static Builder value($column)
 * @method static Builder get($columns = ['*'])
 * @method static Builder paginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null)
 * @method static Builder simplePaginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null)
 * @method static Builder getCountForPagination($columns = ['*'])
 * @method static Builder implode($column, $glue = '')
 * @method static Builder exists()
 * @method static Builder fromRaw($expression, $bindings = [])
 * @method static Builder selectRaw($expression, array $bindings = [])
 * @method static BuildsQueries chunk($count, callable $callback)
 * @method static BuildsQueries each(callable $callback, $count = 1000)
 * @method static BuildsQueries chunkById($count, callable $callback, $column = null, $alias = null)
 * @method static BuildsQueries eachById(callable $callback, $count = 1000, $column = null, $alias = null)
 * @method static BuildsQueries first($columns = ['*'])
 * @method static BuildsQueries when($value, $callback, $default = null)
 * @method static BuildsQueries tap($callback)
 * @method static BuildsQueries unless($value, $callback, $default = null)
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.2
 */
abstract class Model extends \Illuminate\Database\Eloquent\Model
{
}