import React from 'react'
import Typography from '@material-ui/core/Typography';

import AddIcon from '@material-ui/icons/Add';
import SearchIcon from '@material-ui/icons/Search';
import Button from '@material-ui/core/Button';
import TextField from '@material-ui/core/TextField';
import InputAdornment from '@material-ui/core/InputAdornment';
import DesktopWindowsIcon from '@material-ui/icons/DesktopWindows';
import EqualizerIcon from '@material-ui/icons/Equalizer';
import Avatar from '@material-ui/core/Avatar';
import Initializer from '../../../store/Initializer'
import Card from '@material-ui/core/Card';
import CardActions from '@material-ui/core/CardActions';
import CardContent from '@material-ui/core/CardContent';
import { LocalizationTable, TableIcons, removeAccent } from '../../../utils/table.js'
import MaterialTable from "material-table";
import { Grid } from '@material-ui/core';
import { obtenerTodos } from '../../../utils/API/usuarios.js';
import Crear from './componentes/Crear'
import Eliminar from './componentes/Eliminar'
import Filtro from './componentes/Filtro'
import InputLabel from '@material-ui/core/InputLabel';
import MenuItem from '@material-ui/core/MenuItem';
import FormHelperText from '@material-ui/core/FormHelperText';
import FormControl from '@material-ui/core/FormControl';
import Select from '@material-ui/core/Select';
import VisibilityOutlinedIcon from '@material-ui/icons/VisibilityOutlined';
export default function Sistemas(props) {
    const initializer = React.useContext(Initializer);
    const [tipo, setTipo] = React.useState("")

    const [data, setData] = React.useState([])
    const [open, setOpen] = React.useState(false)
    const [open2, setOpen2] = React.useState(false)
    const [selected, setSelected] = React.useState(null)
    const [selected2, setSelected2] = React.useState(null)
    const [openFilter, setOpenFilter] = React.useState(false)

    React.useEffect(() => {
        if (initializer.usuario != null) {
            obtenerTodos(setData, initializer)
        }
    }, [initializer.usuario])
    const carga = () => {
        obtenerTodos(setData, initializer)
        setSelected(null)
        setSelected2(null)
    }
    const total = () => {
        let tot = 0
        data.map((e) => {
            tot += e.evaluaciones
        })
        return tot
    }
    return (
        <Grid container spacing={2}>
            <Crear sistema={selected} setSelected={setSelected} setOpen={setOpen} open={open} carga={carga} />
            <Eliminar sistema={selected2} setOpen={setOpen2} open={open2} carga={carga} />
            <Filtro setOpen={setOpenFilter} open={openFilter} />

            <Grid item xs={12} md={12} style={{ display: 'flex', justifyContent: 'space-between' }}>
                <Typography variant="h5" >
                    Reporte
                </Typography>

            </Grid>

            <Grid item xs={12} md={12}>
                <Card >
                    <CardContent>
                        
                        <FormControl variant="outlined"    style={{ width: '100%' }} >
                            <InputLabel id="demo-simple-select-outlined-label"   style={{ width: '100%' }} >Seleccione el reporte</InputLabel>
                            <Select
                                labelId="demo-simple-select-outlined-label"
                                id="demo-simple-select-outlined"
                                value={tipo}
                                style={{ width: '100%' }}
                                onChange={(e) => setTipo(e.target.value)}
                                label="Seleccione el reporte"
                            >
                                <MenuItem value="">
                                    <em>Seleccione una opción</em>
                                </MenuItem>
                                <MenuItem value={1}>Facturas</MenuItem>
                                <MenuItem value={2}>Kardex</MenuItem>
                                <MenuItem value={3}>Clientes</MenuItem>
                                <MenuItem value={4}>Productos</MenuItem>

                            </Select>
                        </FormControl>
                    </CardContent>
                    <CardActions>
                        <Button variant="contained" disabled={tipo==""} startIcon={<VisibilityOutlinedIcon />} size="small" color="primary">Ver reporte</Button>
                    </CardActions>
                </Card>


            </Grid>

        </Grid>
    )
}
