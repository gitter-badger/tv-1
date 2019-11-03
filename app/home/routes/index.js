export default (route) => {

    route.reg(['index', '/'], 'index').meta({ title: 'title' })

    route.reg(['login', '/login'], 'login').meta({ title: 'µÇÂ¼' })
}

